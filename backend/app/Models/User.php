<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password', 'role'];
    protected $hidden = ['password']; // Hide password when returning user data

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    public function vendorServices()
    {
        return $this->hasMany(VendorService::class);
    }

    public function organizedEvents()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }
}