<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'customer_name',
    'customer_phone',
    'thread_type',
    'order_code',
    'unread_admin_count',
    'unread_customer_count',
    'last_message_at',
])]
class ChatThread extends Model
{
    use HasFactory;

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
}
