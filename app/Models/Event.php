<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_title',
        'event_body',
        'start_date',
        'end_date',
        'event_color',
        'event_border_color',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
