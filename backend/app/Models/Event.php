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
}