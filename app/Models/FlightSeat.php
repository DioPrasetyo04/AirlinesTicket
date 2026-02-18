<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightSeat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_id',
        'name',
        'row',
        'column',
        'class_type',
        'is_available',
    ];

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(TransactionPassenger::class);
    }
}
