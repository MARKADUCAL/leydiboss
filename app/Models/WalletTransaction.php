<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class WalletTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\WalletTransactionFactory> */
    use HasFactory;

    protected $keyType = 'int';

    public $incrementing = false;

    protected $fillable = [
        'customer_id',
        'admin_id',
        'description',
        'type',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-assign the next available ID on creation
        static::creating(function ($transaction) {
            // Find the lowest available ID starting from 1
            $nextId = 1;
            while (static::withoutGlobalScopes()->find($nextId)) {
                $nextId++;
            }
            $transaction->id = $nextId;
        });

        // Renumber IDs when a transaction is deleted
        static::deleted(function ($transaction) {
            $deletedId = $transaction->id;

            // Shift down all transaction IDs greater than the deleted one
            DB::table('wallet_transactions')
                ->where('id', '>', $deletedId)
                ->decrement('id');
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
