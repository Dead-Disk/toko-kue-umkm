<?php

namespace Tests\Feature;

use App\Enums\BentukKue;
use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\CakeAddon;
use App\Models\CakeOption;
use App\Models\Order;
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
        Storage::fake('public');
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
        Storage::fake('public');
        $option = $this->cakeOption();

        $review = $this->post(route('customer.custom.review'), $this->formData($option, [
            'foto_referensi' => UploadedFile::fake()->create('referensi.jpg', 100),
        ]));

        $review->assertOk();
        $review->assertSee('<img src="', false);

        $tempFoto = session('temp_foto');

        $this->assertNotNull($tempFoto);
        $review->assertSee(asset($tempFoto), false);
        $review->assertSee('width="150" style="border: 1px solid #ccc; padding: 4px;"', false);

        $filename = basename($tempFoto);
        Storage::disk('public')->assertExists('temp_custom/'.$filename);

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
        Storage::disk('public')->assertExists($item->foto_referensi);
        Storage::disk('public')->assertMissing('temp_custom/'.$filename);

        $this->assertNull(session('custom_order_draft'));
        $this->assertNull(session('temp_foto'));
    }

    public function test_custom_order_is_saved_without_photo(): void
    {
        Storage::fake('public');
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
        Storage::fake('public');
        $option = $this->cakeOption();

        $this->post(route('customer.custom.review'), $this->formData($option, ['nama_pemesan' => 'Budi']))->assertOk();

        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('value="Budi"', false);
        $response->assertSee('value="'.$option->id.'" selected', false);
    }

    public function test_custom_addons_are_counted_in_total_and_saved(): void
    {
        Storage::fake('public');
        $option = $this->cakeOption();
        $cokelat = CakeAddon::factory()->create(['nama_addon' => 'Cokelat Belgium', 'harga_tambahan' => 20_000]);
        $glutenFree = CakeAddon::factory()->create(['nama_addon' => 'Tepung Gluten-Free', 'harga_tambahan' => 30_000]);

        $review = $this->post(route('customer.custom.review'), $this->formData($option, [
            'jumlah' => 2,
            'addons' => [$cokelat->id, $glutenFree->id],
        ]));

        $review->assertOk();
        $review->assertSee('Cokelat Belgium');
        $review->assertSee('Tepung Gluten-Free');
        $review->assertSee('Rp 400.000'); // (150.000 + 20.000 + 30.000) * 2

        $this->assertDatabaseCount('orders', 0);

        $this->post(route('customer.custom.store'))->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame(400_000, $order->total_harga);

        $item = $order->items()->first();
        $this->assertSame(400_000, $item->subtotal);
        $this->assertSame([
            ['nama_addon' => 'Cokelat Belgium', 'harga_tambahan' => 20_000],
            ['nama_addon' => 'Tepung Gluten-Free', 'harga_tambahan' => 30_000],
        ], $item->addons);
    }

    public function test_custom_review_rejects_unknown_addon(): void
    {
        $option = $this->cakeOption();

        $response = $this->post(route('customer.custom.review'), $this->formData($option, [
            'addons' => [999],
        ]));

        $response->assertSessionHasErrors('addons.0');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_custom_form_lists_addons_as_checkboxes(): void
    {
        $this->cakeOption();
        CakeAddon::factory()->create(['nama_addon' => 'Cokelat Belgium', 'harga_tambahan' => 20_000]);

        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('name="addons[]"', false);
        $response->assertSee('Cokelat Belgium (+Rp 20.000)');
    }

    public function test_custom_order_appears_on_the_admin_order_page(): void
    {
        Storage::fake('public');
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
