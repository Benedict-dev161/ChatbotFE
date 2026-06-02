<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'service_id';

    public $timestamps = false;

    protected $fillable = [
        'service_name',
        'description',
        'price_start',
        'estimated_duration',
    ];

    protected $casts = [
        'price_start' => 'decimal:2',
    ];

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'service_id', 'service_id');
    }

    public function customerForms(): HasMany
    {
        return $this->hasMany(CustomerForm::class, 'service_id', 'service_id');
    }
}
