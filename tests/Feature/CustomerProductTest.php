<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CustomerProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Default: rekening bank & QRIS sudah dikonfigurasi supaya alur pembayaran bisa diuji.
        StoreSetting::factory()->withQris()->create();
        Storage::fake('local');
    }

    /**
     * File bukti pembayaran palsu (GD tidak tersedia di lingkungan test).
     */
    private function paymentProof(string $name = 'bukti.png'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 10, 'image/png');
    }

    /**
     * Langkah 1 (ringkasan pesanan): kirim form pesanan reguler, jangan simpan ke DB.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function postReview(Product $product, array $overrides = []): TestResponse
    {
        return $this->post(route('customer.pesanan.review'), array_merge([
            'product_id' => $product->id,
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'jumlah' => 3,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::TransferBank->value,
        ], $overrides));
    }

    public function test_catalog_orders_ready_products_first_then_habis_alphabetically(): void
    {
        Product::factory()->habis()->create(['nama_kue' => 'Brownies Habis']);
        Product::factory()->create(['nama_kue' => 'Zebra Ready']);
        Product::factory()->habis()->create(['nama_kue' => 'Apel Habis']);
        Product::factory()->create(['nama_kue' => 'Apel Ready']);

        $response = $this->get(route('customer.produk.index'));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Apel Ready',
            'Zebra Ready',
            'Apel Habis',
            'Brownies Habis',
        ]);
    }

    public function test_catalog_disables_order_button_for_out_of_stock_products(): void
    {
        Product::factory()->create(['nama_kue' => 'Kue Ready']);
        $habis = Product::factory()->habis()->create(['nama_kue' => 'Kue Habis']);

        $response = $this->get(route('customer.produk.index'));

        $response->assertOk();
        $response->assertSee('Pesan (Habis)');
        $response->assertSee('disabled', false);
        $response->assertDontSee(route('customer.produk.show', $habis), false);
    }

    public function test_show_displays_product_detail_and_order_form(): void
    {
        $product = Product::factory()->create([
            'nama_kue' => 'Tart Cokelat',
            'harga' => 75_000,
        ]);

        $response = $this->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSee('Tart Cokelat');
        $response->assertSee('Rp75.000');
        $response->assertSee('Form Pesanan');
        $response->assertSee('name="nama_pemesan"', false);
        $response->assertSee('name="alamat_lengkap"', false);
        $response->assertSee('name="nomor_wa"', false);
        $response->assertSee('name="jumlah"', false);
        $response->assertSee('name="opsi_pengiriman"', false);
        $response->assertSee('name="metode_pembayaran"', false);
    }

    public function test_product_page_places_the_direct_order_form_before_the_cart_action(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSeeInOrder(['Form Pesanan Langsung', 'Tambah ke Keranjang']);
    }

    public function test_product_page_limits_quantity_inputs_to_the_product_maximum(): void
    {
        $product = Product::factory()->create(['max_per_order' => 3]);

        $response = $this->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSee('max="3"', false);
        $response->assertSee('data-max-per-order="3"', false);
        $response->assertSee('data-max-order-message="Produk ini hanya dapat dipesan maksimal 3 pcs per transaksi."', false);
    }

    public function test_product_page_renders_flash_error_as_toast(): void
    {
        $product = Product::factory()->create(['max_per_order' => 3]);

        $response = $this->withSession([
            'error' => 'Produk ini hanya dapat dipesan maksimal 3 pcs per transaksi.',
        ])->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSee(
            'data-toast-message="Produk ini hanya dapat dipesan maksimal 3 pcs per transaksi."',
            false
        );
    }

    public function test_order_review_rejects_quantity_above_the_product_limit(): void
    {
        $product = Product::factory()->create(['max_per_order' => 3, 'stok' => 10]);

        $response = $this->from(route('customer.produk.show', $product))
            ->postReview($product, ['jumlah' => 4]);

        $response->assertRedirect(route('customer.produk.show', $product));
        $response->assertSessionHasErrors('jumlah');
        $response->assertSessionHas('error', 'Produk ini hanya dapat dipesan maksimal 3 pcs per transaksi.');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_order_review_accepts_quantity_at_the_product_limit(): void
    {
        $product = Product::factory()->create(['max_per_order' => 3, 'stok' => 10]);

        $response = $this->postReview($product, ['jumlah' => 3]);

        $response->assertOk();
        $response->assertSee('Ringkasan Pesanan');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_review_uses_five_as_the_default_limit(): void
    {
        $product = Product::factory()->create(['stok' => 10]);

        $response = $this->postReview($product, ['jumlah' => 6]);

        $response->assertSessionHasErrors('jumlah');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_rejects_a_draft_that_exceeds_the_current_limit(): void
    {
        $product = Product::factory()->create(['max_per_order' => 5, 'stok' => 10]);

        $this->postReview($product, ['jumlah' => 5]);

        $product->update(['max_per_order' => 2]);

        $response = $this->post(route('customer.pesanan.checkout'), [
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $response->assertRedirect(route('customer.produk.show', $product));
        $response->assertSessionHasErrors('jumlah');
        $response->assertSessionHas('error', 'Produk ini hanya dapat dipesan maksimal 2 pcs per transaksi.');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_saves_order_and_item_then_redirects_to_success_page(): void
    {
        $product = Product::factory()->create([
            'nama_kue' => 'Tart Cokelat',
            'harga' => 75_000,
        ]);

        $this->postReview($product);

        $response = $this->post(route('customer.pesanan.checkout'), [
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $order = Order::firstWhere('nama_pemesan', 'Budi');

        $this->assertNotNull($order);
        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));

        $this->assertSame(TipePesanan::Reguler, $order->tipe_pesanan);
        $this->assertSame(StatusPesanan::Pending, $order->status);
        $this->assertSame(OpsiPengiriman::KurirToko, $order->opsi_pengiriman);
        $this->assertSame(MetodePembayaran::TransferBank, $order->metode_pembayaran);
        $this->assertSame('Jl. Merdeka No. 1', $order->alamat_lengkap);
        $this->assertSame(225_000, $order->total_harga);

        $item = $order->items()->first();

        $this->assertNotNull($item);
        $this->assertSame($product->id, $item->product_id);
        $this->assertSame(3, $item->jumlah);
        $this->assertSame(225_000, $item->subtotal);
    }

    public function test_checkout_generates_order_code_with_expected_format(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product, [
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri->value,
            'metode_pembayaran' => MetodePembayaran::Qris->value,
        ]);

        $this->post(route('customer.pesanan.checkout'), [
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $order = Order::firstOrFail();

        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{4}$/', $order->kode_pesanan);
    }

    public function test_order_review_rejects_products_that_are_out_of_stock(): void
    {
        $product = Product::factory()->habis()->create();

        $response = $this->from(route('customer.produk.show', $product))->postReview($product);

        $response->assertRedirect(route('customer.produk.show', $product));
        $response->assertSessionHasErrors('product_id');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_order_review_accepts_cash_payment_and_shows_its_label(): void
    {
        $product = Product::factory()->create();

        $response = $this->postReview($product, [
            'jumlah' => 1,
            'metode_pembayaran' => MetodePembayaran::Cash->value,
        ]);

        $response->assertOk();
        $response->assertSee('Tunai / Bayar di Toko');

        $this->post(route('customer.pesanan.checkout'));

        $this->assertSame(MetodePembayaran::Cash, Order::firstOrFail()->metode_pembayaran);
    }

    public function test_order_review_validates_required_fields(): void
    {
        $response = $this->post(route('customer.pesanan.review'), []);

        $response->assertSessionHasErrors([
            'product_id',
            'nama_pemesan',
            'alamat_lengkap',
            'nomor_wa',
            'jumlah',
            'opsi_pengiriman',
            'metode_pembayaran',
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_success_page_shows_order_summary(): void
    {
        $order = Order::factory()->create([
            'kode_pesanan' => 'ORD-20261014-0001',
            'nama_pemesan' => 'Siti',
            'total_harga' => 150_000,
        ]);
        $product = Product::factory()->create(['nama_kue' => 'Bolu Pandan']);
        $order->items()->create([
            'product_id' => $product->id,
            'jumlah' => 2,
            'subtotal' => 150_000,
        ]);

        $response = $this->get(route('customer.pesanan.sukses', $order->kode_pesanan));

        $response->assertOk();
        $response->assertSee('ORD-20261014-0001');
        $response->assertSee('menunggu konfirmasi penjual');
        $response->assertSee('Bolu Pandan');
        $response->assertSee('Rp150.000');
    }

    public function test_success_page_links_to_seller_whatsapp_with_order_details(): void
    {
        StoreSetting::current()->update(['store_phone' => '0812-3456-7890']);
        $order = Order::factory()->create([
            'kode_pesanan' => 'ORD-20261014-0002',
            'nama_pemesan' => 'Siti',
            'nomor_wa' => '089912345678',
            'alamat_lengkap' => 'Jl. Melati No. 5',
            'opsi_pengiriman' => OpsiPengiriman::KurirToko,
            'metode_pembayaran' => MetodePembayaran::TransferBank,
            'total_harga' => 170_000,
        ]);
        $boluPandan = Product::factory()->create(['nama_kue' => 'Bolu Pandan']);
        $brownies = Product::factory()->create(['nama_kue' => 'Brownies']);
        $order->items()->create([
            'product_id' => $boluPandan->id,
            'jumlah' => 2,
            'subtotal' => 150_000,
            'catatan' => 'Tanpa kacang',
        ]);
        $order->items()->create([
            'product_id' => $brownies->id,
            'jumlah' => 1,
            'subtotal' => 20_000,
        ]);

        $response = $this->get(route('customer.pesanan.sukses', $order->kode_pesanan));

        $expectedMessage = implode("\n", [
            'Halo, saya sudah membuat pesanan dengan rincian berikut:',
            '',
            '*Kode Pesanan:* ORD-20261014-0002',
            '',
            '*Nama:* Siti',
            '*No HP / WA:* 089912345678',
            '*Alamat:* Jl. Melati No. 5',
            '*Metode Pembayaran:* Transfer Bank',
            '*Opsi Pengiriman:* Diantar Kurir',
            '',
            '*Item Pesanan:*',
            '1. Bolu Pandan',
            '   2 x Rp75.000 = Rp150.000',
            '   Catatan: Tanpa kacang',
            '2. Brownies',
            '   1 x Rp20.000 = Rp20.000',
            '',
            '*Total Harga: Rp170.000*',
        ]);
        $response->assertOk();
        $response->assertViewHas('whatsappUrl', 'https://wa.me/6281234567890?text='.rawurlencode($expectedMessage));
        $response->assertSee('Kirim Pesanan ke WhatsApp Penjual');
    }

    public function test_success_page_hides_whatsapp_button_when_store_phone_is_not_set(): void
    {
        StoreSetting::current()->update(['store_phone' => null]);
        $order = Order::factory()->create();

        $response = $this->get(route('customer.pesanan.sukses', $order->kode_pesanan));

        $response->assertOk();
        $response->assertDontSee('Kirim Pesanan ke WhatsApp Penjual');
    }

    public function test_success_page_returns_not_found_for_unknown_order_code(): void
    {
        $this->get(route('customer.pesanan.sukses', 'ORD-20261014-9999'))->assertNotFound();
    }

    public function test_new_order_appears_on_the_admin_order_page(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['harga' => 50_000]);

        $this->postReview($product, ['jumlah' => 2]);
        $this->post(route('customer.pesanan.checkout'), [
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $order = Order::firstOrFail();

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee($order->kode_pesanan);
        $response->assertSee('Budi');
    }

    public function test_order_review_shows_recap_and_does_not_save_an_order(): void
    {
        $product = Product::factory()->create(['nama_kue' => 'Tart Cokelat', 'harga' => 75_000]);

        $response = $this->postReview($product, ['jumlah' => 2]);

        $response->assertOk();
        $response->assertSee('Ringkasan Pesanan');
        $response->assertSee('Budi');
        $response->assertSee('08123456789');
        $response->assertSee('Jl. Merdeka No. 1');
        $response->assertSee('Diantar Kurir');
        $response->assertSee('Transfer Bank');
        $response->assertSee('Tart Cokelat');
        $response->assertSee('Rp150.000');
        $response->assertSee('Konfirmasi &amp; Kirim Pesanan', false);
        $response->assertSee('Kembali / Edit Pesanan');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_order_form_is_prefilled_from_the_review_draft(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product, [
            'nama_pemesan' => 'Rina',
            'jumlah' => 4,
            'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri->value,
        ]);

        $response = $this->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSee('value="Rina"', false);
        $response->assertSee('value="4"', false);
        $response->assertSee('Ambil Sendiri');
        $response->assertSee('Lanjut ke Ringkasan Pesanan');
    }

    public function test_order_note_is_shown_on_review_and_saved_at_checkout(): void
    {
        $product = Product::factory()->create(['harga' => 50_000]);

        $response = $this->postReview($product, [
            'jumlah' => 1,
            'catatan' => 'Potong jadi 8 bagian dan tambahkan lilin angka 18',
        ]);

        $response->assertOk();
        $response->assertSee('Potong jadi 8 bagian dan tambahkan lilin angka 18');
        $this->assertDatabaseCount('orders', 0);

        $this->post(route('customer.pesanan.checkout'), [
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $this->assertSame(
            'Potong jadi 8 bagian dan tambahkan lilin angka 18',
            Order::firstOrFail()->catatan
        );
    }

    public function test_order_note_is_optional_and_saved_as_null_when_blank(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product);
        $this->post(route('customer.pesanan.checkout'), [
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $this->assertNull(Order::firstOrFail()->catatan);
    }

    public function test_order_review_rejects_a_note_longer_than_1000_characters(): void
    {
        $product = Product::factory()->create();

        $response = $this->postReview($product, ['catatan' => str_repeat('a', 1001)]);

        $response->assertSessionHasErrors('catatan');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_product_page_shows_configured_bank_details(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSee('BNI - 1234567890');
        $response->assertSee('a.n. Toko Kue');
        $response->assertDontSee('Transfer Bank belum dikonfigurasi oleh penjual.');
    }

    public function test_product_page_shows_qris_image_when_configured(): void
    {
        StoreSetting::current()->update(['qris_image_path' => 'qris/qris-toko.png']);
        $product = Product::factory()->create();

        $response = $this->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSee('qris/qris-toko.png', false);
        $response->assertSee('QRIS Toko Kue');
        $response->assertDontSee('QRIS belum dikonfigurasi oleh penjual.');
    }

    public function test_product_page_disables_unconfigured_payment_methods(): void
    {
        StoreSetting::current()->update([
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_holder' => null,
            'qris_image_path' => null,
        ]);
        $product = Product::factory()->create();

        $response = $this->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSee('Transfer Bank belum dikonfigurasi oleh penjual.');
        $response->assertSee('QRIS belum dikonfigurasi oleh penjual.');
        $response->assertSee('disabled', false);
        $response->assertSee('Tunai / Bayar di Toko');
    }

    public function test_order_review_rejects_transfer_bank_when_bank_details_are_missing(): void
    {
        StoreSetting::current()->update([
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_holder' => null,
        ]);
        $product = Product::factory()->create();

        $response = $this->from(route('customer.produk.show', $product))->postReview($product);

        $response->assertRedirect(route('customer.produk.show', $product));
        $response->assertSessionHasErrors([
            'metode_pembayaran' => 'Metode pembayaran belum dikonfigurasi oleh penjual.',
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_review_rejects_qris_when_image_is_missing(): void
    {
        StoreSetting::current()->update(['qris_image_path' => null]);
        $product = Product::factory()->create();

        $response = $this->postReview($product, [
            'metode_pembayaran' => MetodePembayaran::Qris->value,
        ]);

        $response->assertSessionHasErrors([
            'metode_pembayaran' => 'Metode pembayaran belum dikonfigurasi oleh penjual.',
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_review_shows_bank_details_for_selected_transfer(): void
    {
        $product = Product::factory()->create();

        $response = $this->postReview($product);

        $response->assertOk();
        $response->assertSee('Transfer Bank');
        $response->assertSee('BNI - 1234567890');
        $response->assertSee('a.n. Toko Kue');
    }

    public function test_checkout_blocks_transfer_when_bank_details_are_removed_after_review(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product);

        StoreSetting::current()->update([
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_holder' => null,
        ]);

        $response = $this->post(route('customer.pesanan.checkout'));

        $response->assertRedirect(route('customer.produk.show', $product));
        $response->assertSessionHasErrors([
            'metode_pembayaran' => 'Metode pembayaran belum dikonfigurasi oleh penjual.',
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_review_page_requires_payment_proof_for_transfer_bank(): void
    {
        $product = Product::factory()->create();

        $response = $this->postReview($product);

        $response->assertOk();
        $response->assertSee('enctype="multipart/form-data"', false);
        $response->assertSee('name="bukti_pembayaran"', false);
        $response->assertSee('Unggah Bukti Pembayaran (JPG/PNG)');
        $response->assertSee('required', false);
        $response->assertSee('Silakan lakukan pembayaran sesuai nominal di atas, lalu unggah bukti transfer di bawah ini.');
        $response->assertDontSee('Pembayaran dilakukan secara tunai (Cash) saat pengambilan / pengantaran pesanan.');
    }

    public function test_review_page_hides_payment_proof_for_cash(): void
    {
        $product = Product::factory()->create();

        $response = $this->postReview($product, [
            'metode_pembayaran' => MetodePembayaran::Cash->value,
        ]);

        $response->assertOk();
        $response->assertDontSee('name="bukti_pembayaran"', false);
        $response->assertDontSee('Unggah Bukti Pembayaran');
        $response->assertSee('Pembayaran dilakukan secara tunai (Cash) saat pengambilan / pengantaran pesanan.');
        $response->assertDontSee('Pembayaran tunai dilakukan secara langsung di toko / saat penerimaan kue.');
        $response->assertDontSee('Silakan lakukan pembayaran sesuai nominal di atas, lalu unggah bukti transfer di bawah ini.');
    }

    public function test_checkout_requires_payment_proof_for_transfer_bank(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product);

        $response = $this->post(route('customer.pesanan.checkout'));

        $response->assertRedirect(route('customer.produk.show', $product));
        $response->assertSessionHasErrors('bukti_pembayaran');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_requires_payment_proof_for_qris(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product, [
            'metode_pembayaran' => MetodePembayaran::Qris->value,
        ]);

        $response = $this->post(route('customer.pesanan.checkout'));

        $response->assertRedirect(route('customer.produk.show', $product));
        $response->assertSessionHasErrors('bukti_pembayaran');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_checkout_stores_payment_proof_for_transfer_bank(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product);

        $this->post(route('customer.pesanan.checkout'), [
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $order = Order::firstOrFail();

        $this->assertNotNull($order->bukti_pembayaran);
        $this->assertStringStartsWith('bukti_pembayaran/', $order->bukti_pembayaran);
        Storage::disk('local')->assertExists($order->bukti_pembayaran);
    }

    public function test_cash_checkout_does_not_require_payment_proof(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product, [
            'metode_pembayaran' => MetodePembayaran::Cash->value,
        ]);

        $response = $this->post(route('customer.pesanan.checkout'));

        $order = Order::firstOrFail();

        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));
        $this->assertNull($order->bukti_pembayaran);
    }

    public function test_cash_checkout_ignores_a_stray_payment_proof_upload(): void
    {
        $product = Product::factory()->create();

        $this->postReview($product, [
            'metode_pembayaran' => MetodePembayaran::Cash->value,
        ]);

        $response = $this->post(route('customer.pesanan.checkout'), [
            'bukti_pembayaran' => UploadedFile::fake()->create('catatan.txt', 10, 'text/plain'),
        ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));
        $this->assertNull($order->bukti_pembayaran);
    }

    public function test_checkout_without_a_review_draft_redirects_to_the_catalog(): void
    {
        $response = $this->post(route('customer.pesanan.checkout'));

        $response->assertRedirect(route('customer.produk.index'));
        $response->assertSessionHasErrors('order');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_catalog_can_be_filtered_by_search_keyword(): void
    {
        Product::factory()->create(['nama_kue' => 'Tart Pink']);
        Product::factory()->create(['nama_kue' => 'Rainbow Cake']);

        $response = $this->get(route('customer.produk.index', ['search' => 'Pink']));

        $response->assertOk();
        $response->assertSee('Tart Pink');
        $response->assertDontSee('Rainbow Cake');
    }

    public function test_catalog_can_be_filtered_by_category(): void
    {
        $catTart = Category::factory()->create(['nama_kategori' => 'Kue Tart']);
        $catBolu = Category::factory()->create(['nama_kategori' => 'Bolu']);

        Product::factory()->create(['nama_kue' => 'Tart Cokelat', 'category_id' => $catTart->id]);
        Product::factory()->create(['nama_kue' => 'Bolu Gulung', 'category_id' => $catBolu->id]);

        $response = $this->get(route('customer.produk.index', ['category' => $catTart->id]));

        $response->assertOk();
        $response->assertSee('Tart Cokelat');
        $response->assertDontSee('Bolu Gulung');
    }

    public function test_catalog_hides_categories_without_active_products(): void
    {
        $kategoriAktif = Category::factory()->create(['nama_kategori' => 'Kategori Aktif']);
        Product::factory()->create(['category_id' => $kategoriAktif->id]);

        Category::factory()->create(['nama_kategori' => 'Kategori Tanpa Produk']);

        $kategoriTerhapus = Category::factory()->create(['nama_kategori' => 'Kategori Produk Terhapus']);
        Product::factory()->create(['category_id' => $kategoriTerhapus->id])->delete();

        $response = $this->get(route('customer.produk.index'));

        $response->assertOk();
        $response->assertSee('Kategori Aktif');
        $response->assertDontSee('Kategori Tanpa Produk');
        $response->assertDontSee('Kategori Produk Terhapus');
    }

    public function test_catalog_can_be_filtered_by_ready_only(): void
    {
        Product::factory()->create(['nama_kue' => 'Kue Siap']);
        Product::factory()->habis()->create(['nama_kue' => 'Kue Habis']);

        $response = $this->get(route('customer.produk.index', ['ready_only' => 1]));

        $response->assertOk();
        $response->assertSee('Kue Siap');
        $response->assertDontSee('Kue Habis');
    }

    public function test_catalog_can_be_filtered_by_price_range(): void
    {
        Product::factory()->create(['nama_kue' => 'Kue Murah', 'harga' => 15000]);
        Product::factory()->create(['nama_kue' => 'Kue Sedang', 'harga' => 35000]);
        Product::factory()->create(['nama_kue' => 'Kue Premium', 'harga' => 80000]);

        $responseUnder25k = $this->get(route('customer.produk.index', ['price_range' => 'under_25k']));
        $responseUnder25k->assertOk();
        $responseUnder25k->assertSee('Kue Murah');
        $responseUnder25k->assertDontSee('Kue Sedang');
        $responseUnder25k->assertDontSee('Kue Premium');

        $response25k50k = $this->get(route('customer.produk.index', ['price_range' => '25k_50k']));
        $response25k50k->assertOk();
        $response25k50k->assertSee('Kue Sedang');
        $response25k50k->assertDontSee('Kue Murah');
        $response25k50k->assertDontSee('Kue Premium');

        $responseAbove50k = $this->get(route('customer.produk.index', ['price_range' => 'above_50k']));
        $responseAbove50k->assertOk();
        $responseAbove50k->assertSee('Kue Premium');
        $responseAbove50k->assertDontSee('Kue Murah');
        $responseAbove50k->assertDontSee('Kue Sedang');
    }
}
