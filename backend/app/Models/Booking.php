<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $fillable = [
        'venue_id', 
        'organizer_id', 
        'title', 
        'start_date', 
        'end_date', 
        'status'
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
    
    // Add this method to get organizer name easily
    public function getOrganizerNameAttribute()
    {
        return $this->organizer ? $this->organizer->username : 'Unknown';
    }
}