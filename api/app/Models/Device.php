<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $unique_id
 * @property string $language
 * @property string $os
 * @property int $id
 */
class Device extends Model
{
    const OPERATING_SYSTEMS = [
        'google' => 1,
        'ios' => 2
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unique_id',
        'language',
        'os'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
