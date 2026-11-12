<?php

namespace App\Models;

use Database\Factories\ActivityLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'user_name',
    'user_role',
    'action',
    'description',
    'subject_type',
    'subject_id',
    'ip_address',
    'user_agent',
    'properties',
])]
class ActivityLog extends Model
{
    /** @use HasFactory<ActivityLogFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    /**
     * Pengguna yang melakukan aktivitas (bisa null jika user dihapus).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Catat log aktivitas baru ke database.
     *
     * @param  array<string, mixed>  $properties
     */
    public static function record(
        string $action,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?User $user = null
    ): self {
        $actor = $user ?? auth()->user();

        return static::create([
            'user_id' => $actor?->id,
            'user_name' => $actor?->name ?? 'Tamu / Sistem',
            'user_role' => $actor?->role ?? 'admin',
            'action' => strtolower($action),
            'description' => $description,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject ? (string) $subject->getKey() : null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'properties' => $properties ?: null,
        ]);
    }

    /**
     * Label aksi yang rapi untuk ditampilkan di UI.
     */
    public function actionLabel(): string
    {
        return match (strtolower($this->action)) {
            'login' => 'Login',
            'logout' => 'Logout',
            'tambah' => 'Tambah',
            'ubah' => 'Ubah',
            'hapus' => 'Hapus',
            'kasir' => 'Kasir (POS)',
            default => ucfirst($this->action),
        };
    }

    /**
     * Class Tailwind untuk badge aksi.
     */
    public function badgeClasses(): string
    {
        return match (strtolower($this->action)) {
            'login' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'logout' => 'bg-stone-100 text-stone-600 border-stone-200',
            'tambah' => 'bg-teal-50 text-teal-700 border-teal-200',
            'ubah' => 'bg-amber-50 text-amber-800 border-amber-200',
            'hapus' => 'bg-rose-50 text-rose-700 border-rose-200',
            'kasir' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            default => 'bg-stone-50 text-stone-700 border-stone-200',
        };
    }

    /**
     * Class Tailwind untuk badge role.
     */
    public function roleBadgeClasses(): string
    {
        return match (strtolower($this->user_role)) {
            'admin', 'owner' => 'bg-[#6B0F1A]/10 text-[#6B0F1A] border-[#6B0F1A]/20',
            'staff' => 'bg-sky-50 text-sky-700 border-sky-200',
            default => 'bg-stone-100 text-stone-600 border-stone-200',
        };
    }
}
