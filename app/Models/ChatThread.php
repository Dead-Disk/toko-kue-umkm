<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable([
    'customer_name',
    'customer_phone',
    'thread_type',
    'order_code',
    'status',
    'access_token',
    'unread_admin_count',
    'unread_customer_count',
    'last_message_at',
    'closed_at',
])]
class ChatThread extends Model
{
    use HasFactory;

    /**
     * Token asli (plain text) hanya disimpan sementara di memori saat baru dibuat/diverifikasi.
     */
    public ?string $plainAccessToken = null;

    /**
     * Boot model events: generate unique hashed access_token on create if missing.
     */
    protected static function booted(): void
    {
        static::creating(function (ChatThread $thread): void {
            if (empty($thread->access_token)) {
                $plainToken = static::generateUniquePlainToken();
                $thread->plainAccessToken = $plainToken;
                $thread->access_token = static::hashAccessToken($plainToken);
            }
        });
    }

    /**
     * Hash token akses menggunakan SHA-256 sebelum disimpan/dicocokkan di database.
     */
    public static function hashAccessToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    /**
     * Generate 48-char random plain token yang hash-nya dijamin unik di database.
     */
    public static function generateUniquePlainToken(): string
    {
        do {
            $plainToken = Str::random(48);
            $hashedToken = static::hashAccessToken($plainToken);
        } while (static::where('access_token', $hashedToken)->exists());

        return $plainToken;
    }

    /**
     * Cari thread berdasarkan token plain text dari URL pembeli.
     */
    public static function findByPlainToken(string $plainToken): ?self
    {
        $thread = static::with([
            'messages' => fn ($query) => $query->orderBy('id'),
        ])->where('access_token', static::hashAccessToken($plainToken))->first();

        if ($thread) {
            $thread->plainAccessToken = $plainToken;
        }

        return $thread;
    }

    /**
     * Terbitkan token rahasia baru (misalnya saat verifikasi ulang pesanan).
     */
    public function rotateAccessToken(): string
    {
        $plainToken = static::generateUniquePlainToken();
        $this->update([
            'access_token' => static::hashAccessToken($plainToken),
        ]);
        $this->plainAccessToken = $plainToken;

        return $plainToken;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unread_admin_count' => 'integer',
            'unread_customer_count' => 'integer',
            'last_message_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Normalisasi nomor telepon ke format standar (628xxxxxxxx).
     */
    public static function normalizePhone(?string $phone): string
    {
        if (blank($phone)) {
            return '';
        }

        // Hapus semua karakter non-angka
        $clean = preg_replace('/[^\d]/', '', $phone);

        // Ubah awalan 08xxx menjadi 628xxx
        if (str_starts_with($clean, '08')) {
            $clean = '62'.substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62'.$clean;
        }

        return $clean;
    }

    /**
     * Dapatkan URL rahasia bagi pembeli untuk mengakses percakapan ini kembali.
     * Mengembalikan null jika token asli tidak tersedia di memori/sesi saat ini.
     */
    public function getCustomerSecretUrl(?string $plainToken = null): ?string
    {
        $token = $plainToken ?? $this->plainAccessToken;
        if (blank($token)) {
            return null;
        }

        return route('customer.chat.index', ['token' => $token]);
    }

    /**
     * Relasi ke semua pesan dalam thread ini.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'thread_id');
    }

    /**
     * Relasi ke pesan terakhir.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'thread_id')->latestOfMany();
    }

    /**
     * Relasi ke pesanan jika thread ini bertipe 'order'.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_code', 'kode_pesanan');
    }
}
