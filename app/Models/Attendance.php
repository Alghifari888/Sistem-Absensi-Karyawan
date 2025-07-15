<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'check_in_time',
        'check_in_location',
        'check_out_time',
        'check_out_location',
    ];

    /**
     * Mendefinisikan relasi ke model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}