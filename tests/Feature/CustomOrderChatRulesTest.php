<?php

namespace Tests\Feature;

use App\Enums\BentukKue;
use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\CakeOption;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomOrderChatRulesTest extends TestCase
{
    use RefreshDatabase;

    private function createCakeOption(int $harga = 150_000): CakeOption
    {
        $category = Category::factory()->create(['nama_kategori' => 'Tart Custom']);

        return CakeOption::factory()->create([
            'category_id' => $category->id,
            'bentuk' => BentukKue::Lingkaran,
            'ukuran' => '20 cm',
            'harga_dasar' => $harga,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function customFormPayload(CakeOption $option, array $overrides = []): array
    {
        return array_merge([
            'nama_pemesan' => 'Rina Pembeli',
            'nomor_wa' => '081234567890',
            'alamat_lengkap' => 'Jl. Melati No. 10',
            'deskripsi_custom' => 'Tema pastel bunga',
            'cake_option_id' => $option->id,
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::TransferBank->value,
            'tanggal_ambil' => now()->addDays(2)->toDateString(),
        ], $overrides);
    }

    private function createCustomOrderWithThread(
        string $phone = '081234567890',
        string $pin = '123456',
        StatusPesanan $status = StatusPesanan::Pending,
        array $orderOverrides = []
    ): array {
        $order = Order::factory()->custom()->create(array_merge([
            'nama_pemesan' => 'Rina Pembeli',
            'nomor_wa' => $phone,
            'chat_pin' => Hash::make($pin),
            'status' => $status,
            'total_harga' => 150_000,
            'expires_at' => now()->addHours(24),
        ], $orderOverrides));

        OrderItem::factory()->custom()->create([
            'order_id' => $order->id,
            'jumlah' => 1,
            'subtotal' => 150_000,
        ]);

        $thread = ChatThread::create([
            'customer_name' => $order->nama_pemesan,
            'customer_phone' => ChatThread::normalizePhone($phone),
            'thread_type' => 'order',
            'order_code' => $order->kode_pesanan,
            'status' => $status === StatusPesanan::Pending ? 'active' : 'approved',
            'last_message_at' => now(),
        ]);

        return [$order, $thread];
    }

    public function test_phone_with_active_custom_order_is_blocked_from_creating_new_custom_order_or_inquiry_chat(): void
    {
        $option = $this->createCakeOption();
        $this->createCustomOrderWithThread('0812-3456-7890', '654321', StatusPesanan::Pending);

        // 1. Coba buat pesanan custom baru dengan format nomor HP berbeda (+62812...)
        $reviewResponse = $this->from(route('customer.custom.create'))
            ->post(route('customer.custom.review'), $this->customFormPayload($option, [
                'nomor_wa' => '+6281234567890',
            ]));

        $reviewResponse->assertRedirect(route('customer.custom.create'));
        $reviewResponse->assertSessionHasErrors('nomor_wa');
        $reviewResponse->assertSessionHas('active_order_chat_url');
        $this->assertSame(1, Order::where('tipe_pesanan', TipePesanan::Custom)->count());

        // 2. Coba buka ruang chat pra-order baru dengan nomor HP yang sama
        $inquiryResponse = $this->postJson(route('customer.chat.inquiry'), [
            'customer_name' => 'Rina Lagi',
            'customer_phone' => '081234567890',
            'message' => 'Mau buat pesanan baru',
        ]);

        $inquiryResponse->assertUnprocessable()
            ->assertJsonPath('requires_verification', true)
            ->assertJsonStructure(['error', 'requires_verification', 'redirect_url']);
        $this->assertSame(1, ChatThread::count());
    }

    public function test_phone_lock_is_released_when_previous_custom_order_is_acc_completed_cancelled_or_expired(): void
    {
        $option = $this->createCakeOption();

        foreach ([
            ['status' => StatusPesanan::Proses, 'acc_at' => now()],
            ['status' => StatusPesanan::Selesai, 'acc_at' => now()],
            ['status' => StatusPesanan::Ditolak, 'acc_at' => null],
            ['status' => StatusPesanan::Batal, 'acc_at' => null],
            ['status' => StatusPesanan::Expired, 'acc_at' => null],
        ] as $index => $state) {
            $phone = '08130000000'.$index;
            Order::factory()->custom()->create([
                'nomor_wa' => $phone,
                'status' => $state['status'],
                'acc_at' => $state['acc_at'],
            ]);

            $this->post(route('customer.custom.review'), $this->customFormPayload($option, [
                'nomor_wa' => $phone,
            ]))->assertOk();
        }
    }

    public function test_seller_store_phone_and_admin_phone_are_blacklisted_on_guest_order_and_chat_forms(): void
    {
        $option = $this->createCakeOption();
        $product = Product::factory()->create(['stok' => 10, 'harga' => 50_000]);

        StoreSetting::factory()->create(['store_phone' => '0811-2233-4455']);
        User::factory()->create(['role' => 'admin', 'phone' => '087788990011']);
        config(['custom_order.seller_phones' => ['085566778899']]);

        // Nomor toko diblokir di form custom (+62 format)
        $this->from(route('customer.custom.create'))
            ->post(route('customer.custom.review'), $this->customFormPayload($option, [
                'nomor_wa' => '+6281122334455',
            ]))
            ->assertRedirect(route('customer.custom.create'))
            ->assertSessionHasErrors('nomor_wa');

        // Nomor akun admin diblokir di inquiry chat
        $this->postJson(route('customer.chat.inquiry'), [
            'customer_name' => 'Penipu',
            'customer_phone' => '0877-8899-0011',
            'message' => 'Tes',
        ])->assertUnprocessable();

        // Nomor config diblokir di form pesanan reguler & cart checkout
        $this->from(route('customer.produk.show', $product))
            ->post(route('customer.pesanan.review'), [
                'product_id' => $product->id,
                'nama_pemesan' => 'Tamu',
                'alamat_lengkap' => 'Jl. Test',
                'nomor_wa' => '085566778899',
                'jumlah' => 1,
                'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri->value,
                'metode_pembayaran' => MetodePembayaran::Cash->value,
            ])
            ->assertRedirect(route('customer.produk.show', $product))
            ->assertSessionHasErrors('nomor_wa');

        $this->post(route('cart.add', $product));
        $this->post(route('customer.pesanan.checkout'), [
            'checkout_mode' => 'cart',
            'nama_pemesan' => 'Tamu Cart',
            'alamat_lengkap' => 'Jl. Test',
            'nomor_wa' => '081122334455',
            'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri->value,
            'tanggal_ambil' => now()->toDateString(),
            'metode_pembayaran' => MetodePembayaran::Cash->value,
        ])->assertRedirect(route('customer.pesanan.review-cart'))
            ->assertSessionHasErrors('nomor_wa');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_can_add_new_custom_cake_item_inside_active_chat_room(): void
    {
        Storage::fake('local');

        $option = $this->createCakeOption(150_000);
        $baseProduct = Product::factory()->create(['harga' => 200_000, 'nama_kue' => 'Red Velvet']);
        [$order, $thread] = $this->createCustomOrderWithThread('081234567890', '246810');

        // Verifikasi lewat alur Kode Pesanan + No HP + PIN
        $this->post(route('customer.chat.verify-order'), [
            'customer_phone' => '081234567890',
            'order_code' => $order->kode_pesanan,
            'chat_pin' => '246810',
        ])->assertRedirect(route('customer.chat.index'));

        $response = $this->postJson(route('customer.chat.items.store'), [
            'cake_option_id' => $option->id,
            'base_product_id' => $baseProduct->id,
            'jumlah' => 2,
            'deskripsi_custom' => 'Kue kedua warna biru laut',
            'foto_referensi' => UploadedFile::fake()->image('kue2.png', 50, 50),
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.items_count', 2)
            ->assertJsonPath('order.total_harga', 550_000)
            ->assertJsonPath('item.jumlah', 2)
            ->assertJsonPath('item.subtotal', 400_000);

        $order->refresh();
        $this->assertSame(550_000, $order->total_harga);
        $this->assertSame(2, $order->items()->count());

        $addedItem = $order->items()->latest('id')->firstOrFail();
        $this->assertSame('Kue kedua warna biru laut', $addedItem->deskripsi_custom);
        Storage::disk('local')->assertExists($addedItem->foto_referensi);

        $this->assertDatabaseHas('chat_messages', [
            'thread_id' => $thread->id,
            'sender_type' => 'customer',
        ]);
    }

    public function test_adding_custom_item_is_blocked_without_session_or_when_order_is_already_acc_or_closed(): void
    {
        $option = $this->createCakeOption();

        // 1. Tanpa session terverifikasi -> 403
        $this->postJson(route('customer.chat.items.store'), [
            'cake_option_id' => $option->id,
            'jumlah' => 1,
            'deskripsi_custom' => 'Tanpa sesi',
        ])->assertForbidden();

        // 2. Pesanan sudah di-ACC 100% (Proses) -> 422
        [$order] = $this->createCustomOrderWithThread('081234567890', '246810', StatusPesanan::Proses, [
            'acc_at' => now(),
        ]);

        $this->post(route('customer.chat.verify-order'), [
            'customer_phone' => '081234567890',
            'order_code' => $order->kode_pesanan,
            'chat_pin' => '246810',
        ]);

        $this->postJson(route('customer.chat.items.store'), [
            'cake_option_id' => $option->id,
            'jumlah' => 1,
            'deskripsi_custom' => 'Sudah ACC',
        ])->assertUnprocessable();

        $this->assertSame(1, $order->items()->count());
    }

    public function test_seller_can_approve_or_cancel_custom_discussion_from_chat_and_unlock_phone(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $option = $this->createCakeOption();

        // Skenario A: ACC 100% dari chat penjual
        [$orderA, $threadA] = $this->createCustomOrderWithThread('081299990001', '111111');

        $this->actingAs($admin)
            ->post(route('admin.chat.approve', $threadA))
            ->assertRedirect(route('admin.chat.index', ['thread_id' => $threadA->id]));

        $orderA->refresh();
        $threadA->refresh();
        $this->assertSame(StatusPesanan::Proses, $orderA->status);
        $this->assertNotNull($orderA->acc_at);
        $this->assertNull($orderA->expires_at);
        $this->assertSame('approved', $threadA->status);

        // Kunci nomor HP telah terbuka sehingga bisa pesan custom lagi
        $this->post(route('customer.custom.review'), $this->customFormPayload($option, [
            'nomor_wa' => '081299990001',
        ]))->assertOk();

        // Skenario B: Batalkan diskusi dari chat staf berizin
        $staff = User::factory()->create(['role' => 'staff', 'can_chat' => true]);
        [$orderB, $threadB] = $this->createCustomOrderWithThread('081299990002', '222222');

        $this->actingAs($staff)
            ->post(route('staff.chat.cancel', $threadB), ['reason' => 'Pembeli tidak merespons'])
            ->assertRedirect(route('staff.chat.index', ['thread_id' => $threadB->id]));

        $orderB->refresh();
        $threadB->refresh();
        $this->assertSame(StatusPesanan::Batal, $orderB->status);
        $this->assertNotNull($orderB->cancelled_at);
        $this->assertSame('Pembeli tidak merespons', $orderB->cancel_reason);
        $this->assertSame('cancelled', $threadB->status);

        // Kunci nomor HP terbuka kembali
        $this->post(route('customer.custom.review'), $this->customFormPayload($option, [
            'nomor_wa' => '081299990002',
        ]))->assertOk();
    }

    public function test_auto_expire_command_marks_stale_pending_custom_orders_as_expired_and_releases_phone_lock(): void
    {
        $option = $this->createCakeOption();
        [$staleOrder, $staleThread] = $this->createCustomOrderWithThread('081288887777', '123456', StatusPesanan::Pending, [
            'expires_at' => now()->subHour(),
        ]);

        // Pesanan yang sudah ACC tidak boleh di-expire
        [$approvedOrder] = $this->createCustomOrderWithThread('081288886666', '123456', StatusPesanan::Proses, [
            'acc_at' => now()->subDays(2),
            'expires_at' => null,
        ]);

        Artisan::call('custom-orders:expire');

        $staleOrder->refresh();
        $staleThread->refresh();
        $approvedOrder->refresh();

        $this->assertSame(StatusPesanan::Expired, $staleOrder->status);
        $this->assertNotNull($staleOrder->expired_at);
        $this->assertSame('expired', $staleThread->status);
        $this->assertSame(StatusPesanan::Proses, $approvedOrder->status);

        $this->assertDatabaseHas('chat_messages', [
            'thread_id' => $staleThread->id,
            'sender_type' => 'system',
        ]);

        // Nomor HP pesanan expired kini bebas membuat pesanan custom baru
        $this->post(route('customer.custom.review'), $this->customFormPayload($option, [
            'nomor_wa' => '081288887777',
        ]))->assertOk();
    }

    public function test_direct_chat_url_is_protected_without_session_and_persists_across_refresh_after_pin_verification(): void
    {
        [$order, $thread] = $this->createCustomOrderWithThread('081255556666', '987654');
        ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => 'customer',
            'sender_name' => $order->nama_pemesan,
            'message' => 'PESAN_RAHASIA_CUSTOM_ORDER',
        ]);

        // 1. Akses URL langsung tanpa session (simulasi incognito / browser lain) -> ditahan dan diarahkan ke verifikasi PIN
        $unauthResponse = $this->get(route('customer.chat.order', $order->kode_pesanan));
        $unauthResponse->assertRedirect(route('customer.chat.index', [
            'mode' => 'order',
            'order' => $order->kode_pesanan,
        ]));
        $unauthResponse->assertSessionHasErrors('verification');
        $this->assertNull(session('customer_active_thread_id'));

        // Akses token rahasia order yang punya PIN tanpa session juga ditahan
        $this->get(route('customer.chat.index', ['token' => $thread->plainAccessToken]))
            ->assertRedirect(route('customer.chat.index', [
                'mode' => 'order',
                'order' => $order->kode_pesanan,
            ]));

        // Endpoint polling/kirim pesan tanpa session ditolak
        $this->getJson(route('customer.chat.poll'))->assertForbidden();
        $this->postJson(route('customer.chat.send'), ['message' => 'Ilegal'])->assertForbidden();

        // 2. Pembeli melakukan verifikasi (Kode Pesanan + No. HP + PIN)
        $verifyResponse = $this->post(route('customer.chat.verify-order'), [
            'customer_phone' => '081255556666',
            'order_code' => $order->kode_pesanan,
            'chat_pin' => '987654',
        ]);
        $verifyResponse->assertRedirect(route('customer.chat.order', $order->kode_pesanan));

        // 3. Refresh & navigasi ulang tidak meminta PIN lagi selama session valid
        $this->get(route('customer.chat.order', $order->kode_pesanan))
            ->assertOk()
            ->assertSee('PESAN_RAHASIA_CUSTOM_ORDER')
            ->assertSee('+ Tambah Kue Kustom');

        $this->get(route('customer.index'))->assertOk();

        $this->get(route('customer.chat.order', $order->kode_pesanan))
            ->assertOk()
            ->assertSee('PESAN_RAHASIA_CUSTOM_ORDER');

        // 4. Akhiri sesi -> akses langsung URL kembali ditahan
        $this->post(route('customer.chat.end-session'))
            ->assertRedirect(route('customer.chat.index'));

        $this->get(route('customer.chat.order', $order->kode_pesanan))
            ->assertRedirect(route('customer.chat.index', [
                'mode' => 'order',
                'order' => $order->kode_pesanan,
            ]));
    }

    public function test_admin_chat_page_renders_order_threads_with_tipe_pesanan_labels(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$customOrder, $customThread] = $this->createCustomOrderWithThread('081244445555', '123456');

        $regulerOrder = Order::factory()->create([
            'tipe_pesanan' => TipePesanan::Reguler,
            'nama_pemesan' => 'Pembeli Reguler',
            'nomor_wa' => '081244446666',
            'status' => StatusPesanan::Pending,
        ]);
        $regulerThread = ChatThread::create([
            'customer_name' => $regulerOrder->nama_pemesan,
            'customer_phone' => ChatThread::normalizePhone($regulerOrder->nomor_wa),
            'thread_type' => 'order',
            'order_code' => $regulerOrder->kode_pesanan,
            'status' => 'active',
            'last_message_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.chat.index', ['thread_id' => $customThread->id]))
            ->assertOk()
            ->assertSee('Custom Cake')
            ->assertSee($customOrder->kode_pesanan)
            ->assertSee('ACC 100% Pesanan');

        $this->actingAs($admin)
            ->get(route('admin.chat.index', ['thread_id' => $regulerThread->id]))
            ->assertOk()
            ->assertSee('Kue Reguler')
            ->assertSee($regulerOrder->kode_pesanan);
    }
}
