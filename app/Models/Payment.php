<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_date',
        'notes',
        'responsible_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    // Método para obtener el total de pagos de un usuario
    public static function getTotalPaymentsForUser($userId)
    {
        return self::where('user_id', $userId)->sum('amount');
    }

    // Método para obtener los pagos de un usuario en un rango de fechas
    public static function getPaymentsForUserInDateRange($userId, $startDate, $endDate)
    {
        return self::where('user_id', $userId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();
    }

    // Método para obtener el último pago de un usuario
    public static function getLastPaymentForUser($userId)
    {
        return self::where('user_id', $userId)
            ->latest('payment_date')
            ->first();
    }
}
