<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'customer_id',
        'provider_id',
        'category_id',
        'description',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'service_request_id');
    }

    public function appointment()
    {
        return $this->hasOne(Appointment::class, 'service_request_id');
    }
}
