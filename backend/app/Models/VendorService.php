<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorService extends Model
{
    protected $table = 'vendor_services';
    protected $fillable = ['user_id', 'name', 'description', 'price'];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_vendor_services');
    }
}