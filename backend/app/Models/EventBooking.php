<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBooking extends Model
{
    protected $table = 'event_bookings';
    protected $fillable = ['event_id', 'entity_type', 'entity_id', 'status'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}