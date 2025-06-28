<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $fillable = [
    'organizer_id',
    'title',
    'description',
    'start_date',
    'end_date',
    'venue_id',
    'status'          // <— NEW
];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function vendorServices()
    {
        return $this->belongsToMany(VendorService::class, 'event_vendor_services');
    }

    public function bookings()
    {
        return $this->hasMany(EventBooking::class, 'event_id');
    }

    public function updateStatus()
    {
        $bookings = $this->bookings;
        if ($bookings->isEmpty()) {
            $this->status = 'pending';
        } elseif ($bookings->pluck('status')->contains('rejected')) {
            $this->status = 'rejected';
        } elseif ($bookings->pluck('status')->every(fn($status) => $status === 'approved')) {
            $this->status = 'ready';
        } else {
            $this->status = 'pending';
        }
        $this->save();
    }
}