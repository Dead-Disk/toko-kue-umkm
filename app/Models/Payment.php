<?php

namespace App\Models;

use App\Enums\StatusPembayaran;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'order_id',
    'payment_reference',
    'payment_method',
    'amount',
    'status',
    'payment_proof',
    'gateway_response',
    'paid_at',
])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'status' => StatusPembayaran::class,
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Pesanan yang dibayar oleh pembayaran ini.
     *
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
