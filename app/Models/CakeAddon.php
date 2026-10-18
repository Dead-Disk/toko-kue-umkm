<?php

namespace App\Models;

use Database\Factories\CakeAddonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama_addon', 'harga_tambahan'])]
class CakeAddon extends Model
{
    /** @use HasFactory<CakeAddonFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga_tambahan' => 'integer',
        ];
    }
}
