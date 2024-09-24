<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatorBalance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the user that owns the balance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update the balance for a specific user.
     *
     * @param int $userId
     * @param float $amount
     * @return OperatorBalance
     */
    public static function updateBalance($userId, $amount)
    {
        $balance = self::firstOrNew(['user_id' => $userId]);
        $balance->balance += $amount;
        $balance->save();

        return $balance;
    }

    /**
     * Get the current balance for a specific user.
     *
     * @param int $userId
     * @return float
     */
    public static function getCurrentBalance($userId)
    {
        $balance = self::where('user_id', $userId)->first();
        return $balance ? $balance->balance : 0;
    }

    /**
     * Get all balances with user information.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllBalancesWithUsers()
    {
        return self::with('user')->get();
    }

    /**
     * Get balances for users with non-zero balances.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getNonZeroBalances()
    {
        return self::where('balance', '!=', 0)->with('user')->get();
    }

    /**
     * Reset the balance to zero for a specific user.
     *
     * @param int $userId
     * @return OperatorBalance
     */
    public static function resetBalance($userId)
    {
        $balance = self::firstOrNew(['user_id' => $userId]);
        $balance->balance = 0;
        $balance->save();

        return $balance;
    }
}
