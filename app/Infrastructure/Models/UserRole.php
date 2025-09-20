<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $fillable = [
        'user_id',
        'role_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = ['created_at'];
    const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}