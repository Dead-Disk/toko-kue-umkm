<?php

namespace Tests\Feature;

use App\Enums\BentukKue;
use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Models\CakeOption;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChatSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_access_order_chat_with_wrong_order_code(): void
    {
        Order::factory()->create([
            'kode_pesanan' => 'ORD-20261126-1111',
            'nomor_wa' => '081234567890',
            'nama_pemesan' => 'Budi Asli',
            'chat_pin' => Hash::make('123456'),
        ]);

        // Mencoba mengakses dengan nomor HP benar tapi Kode Pesanan salah
        $response = $this->from(route('customer.chat.index'))
            ->post(route('customer.chat.verify-order'), [
                'customer_phone' => '0812-3456-7890',
                'order_code' => 'ORD-SALAH-9999',
                'chat_pin' => '123456',
            ]);

        $response->assertRedirect(route('customer.chat.index'));
        $response->assertSessionHasErrors('verification');
        $this->assertNull(session('customer_active_thread_id'));
    }

    public function test_customer_cannot_access_order_chat_with_mismatched_phone_and_order_code(): void
    {
        Order::factory()->create([
            'kode_pesanan' => 'ORD-20261126-2222',
            'nomor_wa' => '081234567890',
            'nama_pemesan' => 'Korban',
            'chat_pin' => Hash::make('123456'),
        ]);

        // Orang iseng menebak kode pesanan tapi memakai nomor HP miliknya sendiri
        $response = $this->from(route('customer.chat.index'))
            ->post(route('customer.chat.verify-order'), [
                'customer_phone' => '089999999999',
                'order_code' => 'ORD-20261126-2222',
                'chat_pin' => '123456',
            ]);

        $response->assertRedirect(route('customer.chat.index'));
        $response->assertSessionHasErrors('verification');
        $this->assertNull(session('customer_active_thread_id'));
    }

    public function test_customer_cannot_access_order_chat_with_wrong_or_missing_pin(): void
    {
        Order::factory()->create([
            'kode_pesanan' => 'ORD-20261127-4444',
            'nomor_wa' => '081234567890',
            'nama_pemesan' => 'Pembeli Asli',
            'chat_pin' => Hash::make('567890'),
        ]);

        // Admin/staf tahu kode pesanan & nomor WA, tetapi salah menebak PIN
        $response = $this->from(route('customer.chat.index'))
            ->post(route('customer.chat.verify-order'), [
                'customer_phone' => '081234567890',
                'order_code' => 'ORD-20261127-4444',
                'chat_pin' => '123456',
            ]);

        $response->assertRedirect(route('customer.chat.index'));
        $response->assertSessionHasErrors('verification');
        $this->assertNull(session('customer_active_thread_id'));

        // Tanpa PIN harus ditolak validasi
        $this->postJson(route('customer.chat.verify-order'), [
            'customer_phone' => '081234567890',
            'order_code' => 'ORD-20261127-4444',
        ])->assertUnprocessable()->assertJsonValidationErrors('chat_pin');

        // PIN bukan 6 digit (misal 4 digit) harus ditolak validasi
        $this->postJson(route('customer.chat.verify-order'), [
            'customer_phone' => '081234567890',
            'order_code' => 'ORD-20261127-4444',
            'chat_pin' => '5678',
        ])->assertUnprocessable()->assertJsonValidationErrors('chat_pin');
    }

    public function test_customer_can_verify_and_access_order_chat_with_matching_credentials(): void
    {
        $order = Order::factory()->create([
            'kode_pesanan' => 'ORD-20261126-3333',
            'nomor_wa' => '0812-3456-7890',
            'nama_pemesan' => 'Ahmad Pembeli',
            'chat_pin' => Hash::make('432100'),
        ]);

        $response = $this->post(route('customer.chat.verify-order'), [
            'customer_phone' => '+6281234567890', // Format +628...
            'order_code' => 'ORD-20261126-3333',
            'chat_pin' => '432100',
            'message' => 'Halo penjual, apakah pesanan saya sudah dipanggang?',
        ]);

        $response->assertRedirect(route('customer.chat.index'));

        $this->assertDatabaseHas('chat_threads', [
            'order_code' => 'ORD-20261126-3333',
            'customer_phone' => '6281234567890',
            'thread_type' => 'order',
            'unread_admin_count' => 1,
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'message' => 'Halo penjual, apakah pesanan saya sudah dipanggang?',
            'sender_type' => 'customer',
        ]);

        $this->assertNotNull(session('customer_active_thread_id'));
    }

    public function test_pre_order_inquiry_does_not_leak_previous_order_chat_history(): void
    {
        // 1. Pelanggan pernah memesan dan punya thread riwayat order
        $order = Order::factory()->create([
            'kode_pesanan' => 'ORD-20261126-7777',
            'nomor_wa' => '081234567890',
            'nama_pemesan' => 'Budi Pemesan',
        ]);

        $orderThread = ChatThread::create([
            'customer_name' => 'Budi Pemesan',
            'customer_phone' => '6281234567890',
            'thread_type' => 'order',
            'order_code' => 'ORD-20261126-7777',
            'unread_admin_count' => 0,
            'unread_customer_count' => 0,
        ]);

        ChatMessage::create([
            'thread_id' => $orderThread->id,
            'sender_type' => 'customer',
            'sender_name' => 'Budi Pemesan',
            'message' => 'PESAN_RAHASIA_PESANAN_LAMA',
        ]);

        // 2. Seseorang membuka obrolan baru pra-order (Mode 2) hanya memasukkan nomor HP tersebut tanpa kode pesanan
        $response = $this->post(route('customer.chat.inquiry'), [
            'customer_name' => 'Orang Baru',
            'customer_phone' => '081234567890',
            'message' => 'Halo, mau tanya rasa kue yang ready?',
        ]);

        $response->assertRedirect(route('customer.chat.index'));

        // Thread aktif yang tersimpan di sesi harus thread baru, BUKAN thread pesanan lama
        $activeThreadId = session('customer_active_thread_id');
        $this->assertNotEquals($orderThread->id, $activeThreadId);

        // Halaman chat tidak boleh memuat teks rahasia pesanan lama
        $this->get(route('customer.chat.index'))
            ->assertOk()
            ->assertDontSee('PESAN_RAHASIA_PESANAN_LAMA')
            ->assertSee('Halo, mau tanya rasa kue yang ready?');
    }

    public function test_order_success_page_automatically_verifies_chat_session(): void
    {
        $order = Order::factory()->create([
            'kode_pesanan' => 'ORD-20261126-8888',
            'nomor_wa' => '0812-9876-5432',
            'nama_pemesan' => 'Citra',
        ]);

        // Kunjungi halaman pesanan berhasil
        $this->get(route('customer.pesanan.sukses', $order->kode_pesanan))
            ->assertOk();

        // Sesi chat otomatis terverifikasi untuk order tersebut
        $this->assertEquals('ORD-20261126-8888', session('customer_verified_order'));
        $this->assertNotNull(session('customer_active_thread_id'));

        // Buka chat, langsung masuk ke thread pesanan tanpa form verifikasi
        $this->get(route('customer.chat.index'))
            ->assertOk()
            ->assertSee('Terverifikasi Pesanan:')
            ->assertSee('ORD-20261126-8888');
    }

    public function test_customer_can_send_message_in_verified_session(): void
    {
        $thread = ChatThread::create([
            'customer_name' => 'Rian',
            'customer_phone' => '628111222333',
            'thread_type' => 'inquiry',
            'unread_admin_count' => 0,
            'unread_customer_count' => 0,
        ]);

        session(['customer_active_thread_id' => $thread->id]);

        $sendResponse = $this->postJson(route('customer.chat.send'), [
            'message' => 'Pertanyaan di sesi aktif',
        ]);

        $sendResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.message', 'Pertanyaan di sesi aktif');

        $pollResponse = $this->getJson(route('customer.chat.poll'));
        $pollResponse->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonPath('messages.0.id', $sendResponse->json('message.id'));
    }

    public function test_customer_can_send_an_image_without_a_caption_and_only_access_attachments_in_active_thread(): void
    {
        Storage::fake('local');

        $thread = ChatThread::create([
            'customer_name' => 'Rian',
            'customer_phone' => '628111222333',
            'thread_type' => 'inquiry',
        ]);
        session(['customer_active_thread_id' => $thread->id]);

        $response = $this->post(route('customer.chat.send'), [
            'message' => '',
            'image' => UploadedFile::fake()->image('referensi.png', 40, 40),
        ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.message', '');

        $message = ChatMessage::findOrFail($response->json('message.id'));
        $this->assertNotEmpty($message->attachment_path);
        Storage::disk('local')->assertExists($message->attachment_path);

        $attachmentUrl = route('customer.chat.attachment', $message, false);
        $response->assertJsonPath('message.attachment_url', $attachmentUrl);
        $this->get($attachmentUrl)->assertOk();

        $otherThread = ChatThread::create([
            'customer_name' => 'Nia',
            'customer_phone' => '628111222334',
            'thread_type' => 'inquiry',
        ]);
        $otherMessage = ChatMessage::create([
            'thread_id' => $otherThread->id,
            'sender_type' => 'customer',
            'sender_name' => 'Nia',
            'message' => '',
            'attachment_path' => $message->attachment_path,
        ]);

        $this->get(route('customer.chat.attachment', $otherMessage))
            ->assertNotFound();
    }

    public function test_customer_chat_renders_existing_messages_with_ids_in_chronological_order(): void
    {
        $thread = ChatThread::create([
            'customer_name' => 'Rian',
            'customer_phone' => '628111222333',
            'thread_type' => 'inquiry',
        ]);
        $firstMessage = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => 'customer',
            'sender_name' => 'Rian',
            'message' => 'Pesan pembeli pertama',
        ]);
        $secondMessage = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => 'admin',
            'sender_name' => 'Admin',
            'message' => 'Balasan penjual kedua',
        ]);

        session(['customer_active_thread_id' => $thread->id]);

        $this->get(route('customer.chat.index'))
            ->assertOk()
            ->assertSeeInOrder([
                'id="msg-'.$firstMessage->id.'" data-message-id="'.$firstMessage->id.'"',
                'id="msg-'.$secondMessage->id.'" data-message-id="'.$secondMessage->id.'"',
            ], false);

        $this->getJson(route('customer.chat.poll'))
            ->assertOk()
            ->assertJsonPath('messages.0.id', $firstMessage->id)
            ->assertJsonPath('messages.1.id', $secondMessage->id);
    }

    public function test_customer_cannot_send_message_without_active_session(): void
    {
        $sendResponse = $this->postJson(route('customer.chat.send'), [
            'message' => 'Pesan tanpa sesi',
        ]);

        $sendResponse->assertForbidden();
    }

    public function test_admin_has_full_access_to_chat_inbox_and_can_reply(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $thread = ChatThread::create([
            'customer_name' => 'Ahmad',
            'customer_phone' => '628555666777',
            'thread_type' => 'inquiry',
            'unread_admin_count' => 1,
            'unread_customer_count' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertSee('Ahmad')
            ->assertSee('628555666777');

        $replyResponse = $this->actingAs($admin)
            ->postJson(route('admin.chat.reply', $thread), [
                'message' => 'Halo Ahmad, kue ready siap kirim!',
            ]);

        $replyResponse->assertOk()
            ->assertJsonPath('message.sender_type', 'admin')
            ->assertJsonPath('message.sender_badge', 'Admin');

        $this->assertDatabaseHas('chat_messages', [
            'thread_id' => $thread->id,
            'sender_type' => 'admin',
            'sender_id' => $admin->id,
            'message' => 'Halo Ahmad, kue ready siap kirim!',
        ]);
    }

    public function test_admin_can_send_a_captioned_image_and_poll_the_attachment_url(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => 'admin']);
        $thread = ChatThread::create([
            'customer_name' => 'Ahmad',
            'customer_phone' => '628555666777',
            'thread_type' => 'inquiry',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.chat.reply', $thread), [
                'message' => 'Contoh desain kue',
                'image' => UploadedFile::fake()->image('desain.png', 40, 40),
            ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.sender_type', 'admin')
            ->assertJsonPath('message.message', 'Contoh desain kue');

        $message = ChatMessage::findOrFail($response->json('message.id'));
        $this->assertNotEmpty($message->attachment_path);
        Storage::disk('local')->assertExists($message->attachment_path);

        $attachmentUrl = route('admin.chat.attachment', $message, false);
        $response->assertJsonPath('message.attachment_url', $attachmentUrl);
        $this->get($attachmentUrl)->assertOk();

        $this->getJson(route('admin.chat.messages', $thread))
            ->assertOk()
            ->assertJsonPath('messages.0.id', $message->id)
            ->assertJsonPath('messages.0.attachment_url', $attachmentUrl);
    }

    public function test_staff_with_can_chat_permission_can_send_an_image(): void
    {
        Storage::fake('local');

        $staff = User::factory()->create([
            'role' => 'staff',
            'can_chat' => true,
        ]);
        $thread = ChatThread::create([
            'customer_name' => 'Dewi',
            'customer_phone' => '6281234999',
            'thread_type' => 'inquiry',
        ]);

        $response = $this->actingAs($staff)
            ->post(route('staff.chat.reply', $thread), [
                'message' => '',
                'image' => UploadedFile::fake()->image('referensi.jpg', 40, 40),
            ], ['Accept' => 'application/json']);

        $response->assertOk()
            ->assertJsonPath('message.sender_type', 'staff')
            ->assertJsonPath('message.message', '');

        $message = ChatMessage::findOrFail($response->json('message.id'));
        $this->assertNotEmpty($message->attachment_path);
        Storage::disk('local')->assertExists($message->attachment_path);
        $this->get(route('staff.chat.attachment', $message))->assertOk();
    }

    public function test_chat_replies_reject_non_image_and_oversized_attachments(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => 'admin']);
        $thread = ChatThread::create([
            'customer_name' => 'Ahmad',
            'customer_phone' => '628555666777',
            'thread_type' => 'inquiry',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.chat.reply', $thread), [
                'message' => 'Caption',
                'image' => UploadedFile::fake()->create('bukan-gambar.jpg', 10, 'text/plain'),
            ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        $this->post(route('admin.chat.reply', $thread), [
            'message' => 'Caption',
            'image' => UploadedFile::fake()->image('terlalu-besar.png', 40, 40)->size(3073),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        $this->assertDatabaseMissing('chat_messages', [
            'thread_id' => $thread->id,
        ]);
    }

    public function test_active_thread_messages_render_once_in_chronological_order_and_poll_in_id_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $thread = ChatThread::create([
            'customer_name' => 'Ahmad',
            'customer_phone' => '628555666777',
            'thread_type' => 'inquiry',
        ]);
        $firstMessage = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => 'customer',
            'sender_name' => 'Ahmad',
            'message' => 'Pesan pertama',
        ]);
        $secondMessage = ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => 'admin',
            'sender_id' => $admin->id,
            'sender_name' => $admin->name,
            'message' => 'Balasan kedua',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertSeeInOrder([
                'id="admin-msg-'.$firstMessage->id.'" data-message-id="'.$firstMessage->id.'"',
                'id="admin-msg-'.$secondMessage->id.'" data-message-id="'.$secondMessage->id.'"',
            ], false);

        $this->getJson(route('admin.chat.messages', $thread))
            ->assertOk()
            ->assertJsonPath('messages.0.id', $firstMessage->id)
            ->assertJsonPath('messages.1.id', $secondMessage->id);
    }

    public function test_staff_without_can_chat_permission_is_forbidden(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'can_chat' => false,
        ]);

        $this->actingAs($staff)
            ->get(route('staff.chat.index'))
            ->assertForbidden();

        $thread = ChatThread::create([
            'customer_name' => 'Dewi',
            'customer_phone' => '6281234999',
            'thread_type' => 'inquiry',
        ]);

        $this->actingAs($staff)
            ->postJson(route('staff.chat.reply', $thread), [
                'message' => 'Mencoba membalas tanpa izin',
            ])
            ->assertForbidden();
    }

    public function test_staff_with_can_chat_permission_can_access_and_reply(): void
    {
        $staff = User::factory()->create([
            'name' => 'Nisa Staff',
            'role' => 'staff',
            'can_chat' => true,
        ]);

        $thread = ChatThread::create([
            'customer_name' => 'Dewi',
            'customer_phone' => '6281234999',
            'thread_type' => 'order',
            'order_code' => 'ORD-1234',
            'unread_admin_count' => 1,
        ]);

        $this->actingAs($staff)
            ->get(route('staff.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertSee('Dewi');

        $replyResponse = $this->actingAs($staff)
            ->postJson(route('staff.chat.reply', $thread), [
                'message' => 'Halo Kak Dewi, pesanan sedang disiapkan ya.',
            ]);

        $replyResponse->assertOk()
            ->assertJsonPath('message.sender_type', 'staff')
            ->assertJsonPath('message.sender_badge', 'Staf - Nisa Staff');

        $this->assertDatabaseHas('chat_messages', [
            'thread_id' => $thread->id,
            'sender_type' => 'staff',
            'sender_id' => $staff->id,
            'message' => 'Halo Kak Dewi, pesanan sedang disiapkan ya.',
        ]);
    }

    public function test_reply_form_uses_the_active_thread_route_and_includes_csrf_token_for_admin_and_staff(): void
    {
        $thread = ChatThread::create([
            'customer_name' => 'Dewi',
            'customer_phone' => '6281234999',
            'thread_type' => 'inquiry',
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create([
            'role' => 'staff',
            'can_chat' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertSee('action="'.route('admin.chat.reply', $thread, false).'"', false)
            ->assertSee('const messagesUrl = "'.route('admin.chat.messages', $thread, false).'"', false)
            ->assertSee('const threadsUrl = "'.route('admin.chat.threads', [], false).'"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="message"', false);

        $this->actingAs($staff)
            ->get(route('staff.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertSee('action="'.route('staff.chat.reply', $thread, false).'"', false)
            ->assertSee('const messagesUrl = "'.route('staff.chat.messages', $thread, false).'"', false)
            ->assertSee('const threadsUrl = "'.route('staff.chat.threads', [], false).'"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="message"', false);
    }

    public function test_admin_reply_with_an_empty_message_is_rejected_without_saving_a_message(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $thread = ChatThread::create([
            'customer_name' => 'Dewi',
            'customer_phone' => '6281234999',
            'thread_type' => 'inquiry',
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.chat.reply', $thread), [
                'message' => '   ',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');

        $this->assertDatabaseMissing('chat_messages', [
            'thread_id' => $thread->id,
            'sender_type' => 'admin',
        ]);
    }

    public function test_admin_can_toggle_staff_can_chat_permission_via_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create([
            'role' => 'staff',
            'can_chat' => false,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $staff), [
                'name' => $staff->name,
                'username' => $staff->username,
                'email' => $staff->email,
                'role' => 'staff',
                'can_chat' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue($staff->fresh()->can_chat);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $staff), [
                'name' => $staff->name,
                'username' => $staff->username,
                'email' => $staff->email,
                'role' => 'staff',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($staff->fresh()->can_chat);
    }

    public function test_custom_cake_confirmation_automatically_sends_chat_template(): void
    {
        $category = Category::create([
            'nama_kategori' => 'Kue Tart Ulang Tahun',
            'slug' => 'kue-tart-ulang-tahun',
        ]);

        $cakeOption = CakeOption::create([
            'category_id' => $category->id,
            'bentuk' => BentukKue::Lingkaran,
            'ukuran' => '22 cm',
            'harga_dasar' => 175000,
        ]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'nama_kue' => 'Blackforest Special',
            'harga' => 200000,
        ]);

        // Simpan draf custom cake di session seperti saat melewati halaman review
        $draft = [
            'nama_pemesan' => 'Rina Wijaya',
            'nomor_wa' => '0812-3456-7890',
            'alamat_lengkap' => 'Jl. Mawar No. 12, Surabaya',
            'deskripsi_custom' => 'Mohon tulisan Happy 17th Birthday Rina dengan hiasan buah ceri',
            'cake_category_id' => $category->id,
            'cake_option_id' => $cakeOption->id,
            'base_product_id' => $product->id,
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::Qris->value,
            'tanggal_ambil' => now()->addDays(2)->format('Y-m-d'),
            'foto_referensi' => null,
        ];

        session(['custom_order_draft' => $draft]);

        // Pembeli menekan tombol "Konfirmasi & Pesan Sekarang"
        $response = $this->post(route('customer.custom.store'));

        $order = Order::where('nama_pemesan', 'Rina Wijaya')->latest()->first();
        $this->assertNotNull($order);

        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));

        // 1. Thread chat otomatis terbentuk dengan tipe 'order'
        $thread = ChatThread::where('order_code', $order->kode_pesanan)->first();
        $this->assertNotNull($thread);
        $this->assertEquals('6281234567890', $thread->customer_phone);
        $this->assertEquals('order', $thread->thread_type);
        $this->assertEquals(1, $thread->unread_admin_count);

        // 2. Pesan template otomatis tersimpan
        $message = $thread->messages()->first();
        $this->assertNotNull($message);
        $this->assertEquals('customer', $message->sender_type);
        $this->assertStringContainsString("[Pesanan Custom Cake Baru - {$order->kode_pesanan}]", $message->message);
        $this->assertStringContainsString('Rina Wijaya', $message->message);
        $this->assertStringContainsString('Blackforest Special', $message->message);
        $this->assertStringContainsString('Happy 17th Birthday Rina', $message->message);

        // 3. Session browser otomatis terverifikasi untuk chat pesanan tersebut
        $this->assertEquals($thread->id, session('customer_active_thread_id'));
        $this->assertEquals('6281234567890', session('customer_verified_phone'));
        $this->assertEquals($order->kode_pesanan, session('customer_verified_order'));

        // 4. Buka halaman chat, langsung melihat template tanpa perlu verifikasi ulang
        $this->get(route('customer.chat.index'))
            ->assertOk()
            ->assertSee($order->kode_pesanan)
            ->assertSee('Blackforest Special')
            ->assertSee('Happy 17th Birthday Rina');
    }

    public function test_clicking_chat_from_custom_form_forces_verification_form_even_with_prior_session(): void
    {
        // Misalkan ada sesi chat aktif sebelumnya
        $thread = ChatThread::create([
            'customer_name' => 'Budi',
            'customer_phone' => '6281234567890',
            'thread_type' => 'order',
            'order_code' => 'ORD-OLD-999',
            'unread_admin_count' => 0,
            'unread_customer_count' => 0,
        ]);

        session([
            'customer_active_thread_id' => $thread->id,
            'customer_verified_phone' => '6281234567890',
            'customer_verified_order' => 'ORD-OLD-999',
        ]);

        // Pembeli mengklik tautan "Chat Penjual" di bawah form custom cake (dengan query mode=order & verify=1)
        $response = $this->get(route('customer.chat.index', ['mode' => 'order', 'verify' => 1, 'phone' => '081234567890']));

        $response->assertOk();
        // Wajib menampilkan form verifikasi Nomor HP + Kode Pesanan, bukan langsung isi obrolan
        $response->assertSee('Verifikasi Riwayat Pesanan');
        $response->assertSee('Kode Pesanan');
        $response->assertSee('Nomor WhatsApp Pemesan');
        $response->assertDontSee('ORD-OLD-999');

        // Sesi aktif lama harus di-reset
        $this->assertNull(session('customer_active_thread_id'));
    }

    public function test_chat_thread_automatically_generates_unique_access_token_on_creation(): void
    {
        $threadA = ChatThread::create([
            'customer_name' => 'Pembeli A',
            'customer_phone' => '628111111111',
            'thread_type' => 'inquiry',
        ]);

        $threadB = ChatThread::create([
            'customer_name' => 'Pembeli B',
            'customer_phone' => '628111111111',
            'thread_type' => 'inquiry',
        ]);

        $this->assertNotEmpty($threadA->plainAccessToken);
        $this->assertNotEmpty($threadB->plainAccessToken);
        $this->assertSame(48, strlen($threadA->plainAccessToken));
        $this->assertSame(64, strlen($threadA->access_token));
        $this->assertSame(hash('sha256', $threadA->plainAccessToken), $threadA->access_token);
        $this->assertNotEquals($threadA->plainAccessToken, $threadA->access_token);
        $this->assertNotEquals($threadA->access_token, $threadB->access_token);
    }

    public function test_customer_can_reopen_chat_thread_using_valid_secret_token(): void
    {
        $thread = ChatThread::create([
            'customer_name' => 'Sari',
            'customer_phone' => '628123456789',
            'thread_type' => 'inquiry',
        ]);

        ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => 'customer',
            'sender_name' => 'Sari',
            'message' => 'Halo, apakah kue lapis legit masih tersedia?',
        ]);

        // Tanpa sesi aktif, pembeli membuka chat lewat tautan rahasia (plain token)
        $response = $this->get(route('customer.chat.index', ['token' => $thread->plainAccessToken]));

        $response->assertOk()
            ->assertSee('Halo, apakah kue lapis legit masih tersedia?')
            ->assertSee('Tautan Rahasia Obrolan Anda')
            ->assertSee($thread->getCustomerSecretUrl(), false);

        $this->assertSame($thread->id, session('customer_active_thread_id'));
    }

    public function test_invalid_secret_token_shows_verification_form_without_leaking_threads(): void
    {
        $thread = ChatThread::create([
            'customer_name' => 'Rahasia',
            'customer_phone' => '628123456789',
            'thread_type' => 'inquiry',
        ]);

        ChatMessage::create([
            'thread_id' => $thread->id,
            'sender_type' => 'customer',
            'sender_name' => 'Rahasia',
            'message' => 'PESAN_TIDAK_BOLEH_BOCOR',
        ]);

        $this->get(route('customer.chat.index', ['token' => 'token-palsu-tidak-valid']))
            ->assertOk()
            ->assertDontSee('PESAN_TIDAK_BOLEH_BOCOR')
            ->assertSee('Diskusi Pesanan');

        // Hash di database tidak boleh bisa dipakai langsung sebagai token di URL
        $this->get(route('customer.chat.index', ['token' => $thread->access_token]))
            ->assertOk()
            ->assertDontSee('PESAN_TIDAK_BOLEH_BOCOR');

        $this->assertNull(session('customer_active_thread_id'));
    }

    public function test_admin_and_staff_cannot_see_customer_secret_link_or_plain_token(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff', 'can_chat' => true]);

        $thread = ChatThread::create([
            'customer_name' => 'Rahasia Pembeli',
            'customer_phone' => '628123456789',
            'thread_type' => 'inquiry',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertDontSee('Salin Tautan')
            ->assertDontSee($thread->plainAccessToken);

        $this->actingAs($staff)
            ->get(route('staff.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertDontSee('Salin Tautan')
            ->assertDontSee($thread->plainAccessToken);
    }

    public function test_secret_token_takes_priority_over_existing_active_session(): void
    {
        $threadA = ChatThread::create([
            'customer_name' => 'Pembeli A',
            'customer_phone' => '628111111111',
            'thread_type' => 'inquiry',
        ]);
        ChatMessage::create([
            'thread_id' => $threadA->id,
            'sender_type' => 'customer',
            'sender_name' => 'Pembeli A',
            'message' => 'ISI_CHAT_THREAD_A',
        ]);

        $threadB = ChatThread::create([
            'customer_name' => 'Pembeli B',
            'customer_phone' => '628222222222',
            'thread_type' => 'inquiry',
        ]);
        ChatMessage::create([
            'thread_id' => $threadB->id,
            'sender_type' => 'customer',
            'sender_name' => 'Pembeli B',
            'message' => 'ISI_CHAT_THREAD_B',
        ]);

        session(['customer_active_thread_id' => $threadA->id]);

        $response = $this->get(route('customer.chat.index', ['token' => $threadB->plainAccessToken]));

        $response->assertOk()
            ->assertSee('ISI_CHAT_THREAD_B')
            ->assertDontSee('ISI_CHAT_THREAD_A');

        $this->assertSame($threadB->id, session('customer_active_thread_id'));
    }

    public function test_verify_order_start_inquiry_and_poll_return_token_and_secret_url_in_json(): void
    {
        $order = Order::factory()->create([
            'kode_pesanan' => 'ORD-20261127-9999',
            'nomor_wa' => '081234567890',
            'nama_pemesan' => 'Dina',
            'chat_pin' => Hash::make('987654'),
        ]);

        $verifyResponse = $this->postJson(route('customer.chat.verify-order'), [
            'customer_phone' => '081234567890',
            'order_code' => 'ORD-20261127-9999',
            'chat_pin' => '987654',
        ]);

        $verifyResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'thread_id', 'token', 'secret_url']);

        $inquiryResponse = $this->postJson(route('customer.chat.inquiry'), [
            'customer_name' => 'Dina Tanya',
            'customer_phone' => '081234567890',
            'message' => 'Halo admin',
        ]);

        $inquiryResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'thread_id', 'token', 'secret_url']);

        $pollResponse = $this->getJson(route('customer.chat.poll'));
        $pollResponse->assertOk()
            ->assertJsonPath('thread.token', $inquiryResponse->json('token'))
            ->assertJsonPath('thread.secret_url', $inquiryResponse->json('secret_url'));
    }

    public function test_start_inquiry_and_verify_order_are_rate_limited(): void
    {
        // Batas inquiry: 5 percobaan per 60 detik per IP
        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('customer.chat.inquiry'), [
                'customer_name' => "Spam {$i}",
                'customer_phone' => '081234567890',
            ])->assertOk();
        }

        $this->postJson(route('customer.chat.inquiry'), [
            'customer_name' => 'Spam Blocked',
            'customer_phone' => '081234567890',
        ])->assertStatus(429);

        // Batas verifyOrder: 10 percobaan per 60 detik per IP
        for ($i = 0; $i < 10; $i++) {
            $this->postJson(route('customer.chat.verify-order'), [
                'customer_phone' => '081234567890',
                'order_code' => 'ORD-TIDAK-ADA',
                'chat_pin' => '123456',
            ])->assertStatus(422);
        }

        $blockedVerify = $this->postJson(route('customer.chat.verify-order'), [
            'customer_phone' => '081234567890',
            'order_code' => 'ORD-TIDAK-ADA',
            'chat_pin' => '123456',
        ]);

        $blockedVerify->assertStatus(422);
        $this->assertStringContainsString('Terlalu banyak percobaan verifikasi', $blockedVerify->json('error'));
    }

    public function test_read_and_unread_message_status_updates_across_customer_admin_and_staff(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff', 'can_chat' => true]);

        $thread = ChatThread::create([
            'customer_name' => 'Rina',
            'customer_phone' => '628123456789',
            'thread_type' => 'inquiry',
        ]);

        // 1. Pembeli mengirim pesan -> awalnya is_read = false ("Terkirim")
        session(['customer_active_thread_id' => $thread->id]);
        $customerSend = $this->postJson(route('customer.chat.send'), [
            'message' => 'Halo, apakah ready?',
        ]);
        $customerSend->assertOk()->assertJsonPath('message.is_read', false);

        $this->get(route('customer.chat.index'))
            ->assertOk()
            ->assertSee('data-is-read="0"', false)
            ->assertSee('Terkirim');

        // 2. Admin membuka thread -> pesan pembeli otomatis ditandai terbaca
        $this->actingAs($admin)
            ->get(route('admin.chat.index', ['thread_id' => $thread->id]))
            ->assertOk();

        $this->getJson(route('customer.chat.poll'))
            ->assertOk()
            ->assertJsonPath('messages.0.is_read', true);

        $this->get(route('customer.chat.index'))
            ->assertOk()
            ->assertSee('data-is-read="1"', false)
            ->assertSee('Dibaca');

        // 3. Staf membalas pesan -> awalnya is_read = false ("Terkirim")
        $staffReply = $this->actingAs($staff)
            ->postJson(route('staff.chat.reply', $thread), [
                'message' => 'Ready kak, silakan dipesan.',
            ]);
        $staffReply->assertOk()->assertJsonPath('message.is_read', false);

        $this->actingAs($staff)
            ->get(route('staff.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertSee('data-is-read="0"', false)
            ->assertSee('Terkirim');

        // 4. Pembeli membuka chat lagi -> balasan staf otomatis ditandai terbaca
        $this->get(route('customer.chat.index', ['token' => $thread->plainAccessToken]))
            ->assertOk();

        $this->actingAs($staff)
            ->getJson(route('staff.chat.messages', $thread))
            ->assertOk()
            ->assertJsonPath('messages.1.is_read', true);

        $this->actingAs($admin)
            ->get(route('admin.chat.index', ['thread_id' => $thread->id]))
            ->assertOk()
            ->assertSee('data-is-read="1"', false)
            ->assertSee('Dibaca');
    }

    public function test_checkout_saves_hashed_chat_pin_and_allows_order_chat_verification(): void
    {
        $product = Product::factory()->create([
            'stok' => 10,
            'harga' => 50000,
        ]);

        // 1. Pembeli mengisi form review pesanan tanpa PIN; halaman review memuat popup pembuatan PIN
        $this->post(route('customer.pesanan.review'), [
            'product_id' => $product->id,
            'nama_pemesan' => 'Siti Checkout',
            'alamat_lengkap' => 'Jl. Melati No. 5',
            'nomor_wa' => '081234567890',
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri->value,
            'metode_pembayaran' => MetodePembayaran::Cash->value,
        ])->assertOk()->assertSee('chatPinModal');

        // 2. Pembeli konfirmasi checkout beserta PIN 6 digit dari popup (246810)
        $this->post(route('customer.pesanan.checkout'), [
            'chat_pin' => '246810',
        ])->assertRedirect();

        $order = Order::where('nama_pemesan', 'Siti Checkout')->firstOrFail();
        $this->assertNotNull($order->chat_pin);
        $this->assertNotSame('246810', $order->chat_pin);
        $this->assertTrue($order->verifyChatPin('246810'));
        $this->assertFalse($order->verifyChatPin('000000'));

        // 3. Verifikasi chat pesanan menggunakan PIN 246810 berhasil
        $this->postJson(route('customer.chat.verify-order'), [
            'customer_phone' => '081234567890',
            'order_code' => $order->kode_pesanan,
            'chat_pin' => '246810',
        ])->assertOk()->assertJsonPath('success', true);
    }

    public function test_confirmed_pin_is_reused_after_the_review_page_is_reopened(): void
    {
        $category = Category::create([
            'nama_kategori' => 'Kue Tart Ulang Tahun',
            'slug' => 'kue-tart-ulang-tahun',
        ]);
        $cakeOption = CakeOption::create([
            'category_id' => $category->id,
            'bentuk' => BentukKue::Lingkaran,
            'ukuran' => '22 cm',
            'harga_dasar' => 175000,
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'nama_kue' => 'Blackforest Special',
            'harga' => 200000,
        ]);

        $draft = [
            'nama_pemesan' => 'Rina Wijaya',
            'nomor_wa' => '0812-3456-7890',
            'alamat_lengkap' => 'Jl. Mawar No. 12, Surabaya',
            'deskripsi_custom' => 'Tulisan Happy Birthday',
            'cake_category_id' => $category->id,
            'cake_option_id' => $cakeOption->id,
            'base_product_id' => $product->id,
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::Qris->value,
            'tanggal_ambil' => now()->addDays(2)->format('Y-m-d'),
            'foto_referensi' => null,
        ];

        $this->postJson(route('customer.chat.pin.store'), ['chat_pin' => '135790'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame('135790', session('customer_chat_pin'));

        $this->post(route('customer.custom.review'), $draft)
            ->assertOk()
            ->assertSee('135790', false);

        $this->post(route('customer.custom.store'))->assertRedirect();

        $order = Order::where('nama_pemesan', 'Rina Wijaya')->firstOrFail();

        $this->assertTrue($order->verifyChatPin('135790'));
        $this->assertFalse($order->verifyChatPin('000000'));
        $this->assertNull(session('customer_chat_pin'));
    }

    public function test_single_order_checkout_falls_back_to_the_pin_saved_in_session(): void
    {
        $product = Product::factory()->create([
            'stok' => 10,
            'harga' => 50000,
        ]);

        $this->post(route('customer.pesanan.review'), [
            'product_id' => $product->id,
            'nama_pemesan' => 'Siti Checkout',
            'alamat_lengkap' => 'Jl. Melati No. 5',
            'nomor_wa' => '081234567890',
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri->value,
            'metode_pembayaran' => MetodePembayaran::Cash->value,
        ])->assertOk();

        $this->postJson(route('customer.chat.pin.store'), ['chat_pin' => '246810'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->post(route('customer.pesanan.checkout'))->assertRedirect();

        $order = Order::where('nama_pemesan', 'Siti Checkout')->firstOrFail();

        $this->assertTrue($order->verifyChatPin('246810'));
        $this->assertNull(session('customer_chat_pin'));
    }

    public function test_cart_checkout_falls_back_to_the_pin_saved_in_session(): void
    {
        $product = Product::factory()->create([
            'stok' => 10,
            'harga' => 50000,
        ]);

        $this->post(route('cart.add', $product));

        $this->postJson(route('customer.chat.pin.store'), ['chat_pin' => '864202'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->post(route('customer.pesanan.checkout'), [
            'checkout_mode' => 'cart',
            'nama_pemesan' => 'Budi Cart',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'tanggal_ambil' => now()->toDateString(),
            'metode_pembayaran' => MetodePembayaran::Cash->value,
        ])->assertRedirect();

        $order = Order::where('nama_pemesan', 'Budi Cart')->firstOrFail();

        $this->assertTrue($order->verifyChatPin('864202'));
        $this->assertNull(session('customer_chat_pin'));
    }

    public function test_storing_an_invalid_pin_is_rejected_without_changing_the_session(): void
    {
        $this->postJson(route('customer.chat.pin.store'), ['chat_pin' => '12345'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['chat_pin' => 'PIN rahasia chat harus tepat 6 digit angka.']);

        $this->assertNull(session('customer_chat_pin'));
    }
}
