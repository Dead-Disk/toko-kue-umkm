<?php

namespace Tests\Feature;

use App\Enums\BentukKue;
use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\CakeOption;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Opsi ukuran kue standar untuk test (150.000 per kue).
     */
    private function cakeOption(): CakeOption
    {
        return CakeOption::factory()->create([
            'bentuk' => BentukKue::Lingkaran,
            'ukuran' => '20 cm',
            'harga_dasar' => 150_000,
        ]);
    }

    /**
     * Data form custom yang valid.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function formData(CakeOption $option, array $overrides = []): array
    {
        return array_merge([
            'nama_pemesan' => 'Siti',
            'nomor_wa' => '08123456789',
            'alamat_lengkap' => 'Jl. Mawar No. 2',
            'deskripsi_custom' => 'Tulisan Happy Birthday',
            'cake_option_id' => $option->id,
            'jumlah' => 2,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::TransferBank->value,
        ], $overrides);
    }

    public function test_custom_form_submission_shows_review_without_saving_order(): void
    {
        Storage::fake('local');
        $option = $this->cakeOption();

        $response = $this->post(route('customer.custom.review'), $this->formData($option));

        $response->assertOk();
        $response->assertSee('Review Pesanan Custom');
        $response->assertSee('Konfirmasi &amp; Pesan Sekarang', false);
        $response->assertSee('Lingkaran 20 cm - Rp 150.000');
        $response->assertSee('Rp 300.000');
        $response->assertSee('Kembali / Edit Pesanan');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_custom_order_is_saved_with_uploaded_photo_after_confirmation(): void
    {
        Storage::fake('local');
        $option = $this->cakeOption();

        $review = $this->post(route('customer.custom.review'), $this->formData($option, [
            'foto_referensi' => UploadedFile::fake()->create('referensi.jpg', 100),
        ]));

        $review->assertOk();
        $review->assertSee('Foto referensi sudah diunggah.');

        $tempFoto = session('temp_foto');

        $this->assertNotNull($tempFoto);
        $this->assertStringStartsWith('temp_custom/', $tempFoto);

        $filename = basename($tempFoto);
        Storage::disk('local')->assertExists('temp_custom/'.$filename);

        $response = $this->post(route('customer.custom.store'));

        $order = Order::firstWhere('nama_pemesan', 'Siti');

        $this->assertNotNull($order);
        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));

        $this->assertSame(TipePesanan::Custom, $order->tipe_pesanan);
        $this->assertSame(StatusPesanan::Pending, $order->status);
        $this->assertSame(OpsiPengiriman::KurirToko, $order->opsi_pengiriman);
        $this->assertSame(MetodePembayaran::TransferBank, $order->metode_pembayaran);
        $this->assertSame(300_000, $order->total_harga);

        $item = $order->items()->first();

        $this->assertNotNull($item);
        $this->assertNull($item->product_id);
        $this->assertSame(2, $item->jumlah);
        $this->assertSame(300_000, $item->subtotal);
        $this->assertSame('Lingkaran 20 cm - Rp 150.000', $item->ukuran_kue);
        $this->assertSame('Tulisan Happy Birthday', $item->deskripsi_custom);
        $this->assertSame('custom_cakes/'.$filename, $item->foto_referensi);
        Storage::disk('local')->assertExists($item->foto_referensi);
        Storage::disk('local')->assertMissing('temp_custom/'.$filename);

        $this->assertNull(session('custom_order_draft'));
        $this->assertNull(session('temp_foto'));
    }

    public function test_custom_order_is_saved_without_photo(): void
    {
        Storage::fake('local');
        $option = $this->cakeOption();

        $review = $this->post(route('customer.custom.review'), $this->formData($option, ['jumlah' => 1]));
        $review->assertOk();
        $review->assertSee('<span>Tidak ada foto referensi</span>', false);

        $this->post(route('customer.custom.store'))->assertRedirect();

        $item = Order::firstOrFail()->items()->first();

        $this->assertNotNull($item);
        $this->assertNull($item->foto_referensi);
        $this->assertSame(1, $item->jumlah);
        $this->assertSame(150_000, $item->subtotal);
    }

    public function test_custom_review_validates_required_fields(): void
    {
        $response = $this->post(route('customer.custom.review'), []);

        $response->assertSessionHasErrors([
            'nama_pemesan',
            'nomor_wa',
            'alamat_lengkap',
            'deskripsi_custom',
            'cake_option_id',
            'jumlah',
            'opsi_pengiriman',
            'metode_pembayaran',
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_custom_store_requires_a_review_draft(): void
    {
        $response = $this->post(route('customer.custom.store'));

        $response->assertRedirect(route('customer.custom.create'));
        $response->assertSessionHasErrors('custom');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_custom_form_shows_cake_options_from_database(): void
    {
        $this->cakeOption();

        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('name="cake_option_id"', false);
        $response->assertSee('Lingkaran 20 cm - Rp 150.000');
    }

    public function test_custom_form_is_prefilled_from_the_saved_review_draft(): void
    {
        Storage::fake('local');
        $option = $this->cakeOption();

        $this->post(route('customer.custom.review'), $this->formData($option, ['nama_pemesan' => 'Budi']))->assertOk();

        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('value="Budi"', false);
        $response->assertSee('value="'.$option->id.'" selected', false);
    }

    public function test_custom_form_shows_base_products_and_the_live_estimate_preview(): void
    {
        $product = Product::factory()->create([
            'nama_kue' => 'Kue Cokelat Acuan',
            'harga' => 200_000,
        ]);
        $this->cakeOption();

        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('name="base_product_id"', false);
        $response->assertSee('Kue Cokelat Acuan');
        $response->assertSee('data-harga="200000"', false);
        $response->assertSee('id="estimasi-total"', false);
        $response->assertSee('Estimasi Total:');
        $response->assertSee('name="cake_option_id"', false);
    }

    public function test_custom_form_limits_quantity_to_five(): void
    {
        $this->cakeOption();

        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('max="5"', false);
        $response->assertSee('data-max-quantity="5"', false);
        $response->assertSee('Maksimal pemesanan untuk pesanan ini adalah 5 pcs.', false);
    }

    public function test_custom_review_rejects_quantity_above_five(): void
    {
        $option = $this->cakeOption();

        $response = $this->post(route('customer.custom.review'), $this->formData($option, [
            'jumlah' => 6,
        ]));

        $response->assertSessionHasErrors([
            'jumlah' => 'Maksimal pemesanan untuk pesanan ini adalah 5 pcs.',
        ]);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_custom_form_resets_quantity_above_five_from_old_input(): void
    {
        $option = $this->cakeOption();
        $product = Product::factory()->create(['harga' => 200_000]);

        $this->from(route('customer.custom.create'))
            ->post(route('customer.custom.review'), $this->formData($option, [
                'base_product_id' => $product->id,
                'jumlah' => 6,
            ]))
            ->assertRedirect(route('customer.custom.create'));

        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('id="jumlah" name="jumlah" value="5"', false);
        $response->assertSee('Estimasi Total: Rp 1.000.000');
    }

    public function test_custom_form_prefills_the_selected_base_product_from_the_draft(): void
    {
        Storage::fake('local');
        $option = $this->cakeOption();
        $product = Product::factory()->create(['harga' => 200_000]);

        $this->post(route('customer.custom.review'), $this->formData($option, [
            'base_product_id' => $product->id,
        ]))->assertOk();

        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('value="'.$product->id.'" selected', false);
        $response->assertSee('Estimasi Total: Rp 400.000');
    }

    public function test_custom_review_uses_the_base_product_price_for_the_estimate(): void
    {
        Storage::fake('local');
        $option = $this->cakeOption();
        $product = Product::factory()->create([
            'nama_kue' => 'Kue Cokelat Acuan',
            'harga' => 200_000,
        ]);

        $response = $this->post(route('customer.custom.review'), $this->formData($option, [
            'base_product_id' => $product->id,
        ]));

        $response->assertOk();
        $response->assertSee('Produk Acuan:');
        $response->assertSee('Kue Cokelat Acuan');
        $response->assertSeeText('Produk Acuan: Kue Cokelat Acuan');
        $response->assertSee('Harga Dasar / Acuan');
        $response->assertSee('Estimasi Total Harga');
        $response->assertSee('Rp 400.000');
        $response->assertDontSee('Rp 300.000');
    }

    public function test_custom_review_rejects_an_unavailable_base_product(): void
    {
        Storage::fake('local');
        $option = $this->cakeOption();
        $product = Product::factory()->create();
        $product->delete();

        $response = $this->post(route('customer.custom.review'), $this->formData($option, [
            'base_product_id' => $product->id,
        ]));

        $response->assertSessionHasErrors('base_product_id');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_custom_order_saves_the_base_product_reference_and_price(): void
    {
        Storage::fake('local');
        $option = $this->cakeOption();
        $product = Product::factory()->create(['harga' => 200_000]);

        $this->post(route('customer.custom.review'), $this->formData($option, [
            'base_product_id' => $product->id,
        ]))->assertOk();

        $this->post(route('customer.custom.store'))->assertRedirect();

        $order = Order::firstOrFail();
        $item = $order->items()->firstOrFail();

        $this->assertSame(400_000, $order->total_harga);
        $this->assertSame($product->id, $item->product_id);
        $this->assertSame(400_000, $item->subtotal);
    }

    public function test_custom_order_appears_on_the_admin_order_page(): void
    {
        Storage::fake('local');
        $seller = User::factory()->create();
        $option = $this->cakeOption();

        $this->post(route('customer.custom.review'), $this->formData($option))->assertOk();
        $this->post(route('customer.custom.store'))->assertRedirect();

        $order = Order::firstOrFail();

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee($order->kode_pesanan);
        $response->assertSee('Siti');
    }
}
