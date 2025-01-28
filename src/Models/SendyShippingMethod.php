<?php

namespace Dashed\DashedEcommerceMyParcel\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MyParcelShippingMethod extends Model
{
    use LogsActivity;

    protected static $logFillable = true;

    protected $table = 'dashed__myparcel_shipping_methods';

    protected $fillable = [
        'name',
        'value',
        'site_id',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function MyParcelShippingMethodServices()
    {
        return $this->hasMany(MyParcelShippingMethodService::class);
    }
}
