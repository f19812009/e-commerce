<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'status',
        'courier_name', 'tracking_code', 'tracking_note',
        'subtotal', 'shipping', 'total',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'city', 'postal_code', 'country',
        'payment_method', 'payment_status', 'notes',
    ];

    // ---------- Boot ----------

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    // ---------- Relationships ----------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ---------- Accessors ----------

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'amber',
            'processing' => 'blue',
            'shipped'    => 'indigo',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            default      => 'slate',
        };
    }
}
