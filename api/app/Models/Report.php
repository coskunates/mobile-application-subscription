<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $application_id
 * @property string $date
 * @property string $event
 * @property int $count
 * @property int $id
 * @property int $os
 */
class Report extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'application_id',
        'date',
        'event'
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
