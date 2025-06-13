<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venues';
    protected $fillable = ['user_id', 'name', 'address', 'capacity', 'description', 'price', 'image'];
    
    // Disable timestamps if your table doesn't have created_at/updated_at columns
    public $timestamps = false;

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}