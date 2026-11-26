<?php

namespace App\Models;

use App\Enums\BentukKue;
use Database\Factories\CakeOptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['bentuk', 'ukuran', 'harga_dasar', 'category_id'])]
class CakeOption extends Model
{
    /** @use HasFactory<CakeOptionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bentuk' => BentukKue::class,
            'harga_dasar' => 'integer',
        ];
    }

    /**
     * Kategori kue yang terkait dengan opsi varian ini.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Label tampilan opsi, contoh: "Lingkaran 20 cm - Rp 150.000".
     */
    public function label(): string
    {
        return sprintf(
            '%s %s - Rp %s',
            $this->bentuk->label(),
            $this->ukuran,
            number_format($this->harga_dasar, 0, ',', '.')
        );
    }
}
