<?php

namespace Dashed\DashedEcommerceMyParcel\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MyParcelShippingMethodServiceOption extends Model
{
    use LogsActivity;

    protected static $logFillable = true;

    protected $table = 'dashed__myparcel_shipping_method_service_options';

    protected $fillable = [
        'myparcel_shipping_method_service_id',
        'name',
        'field',
        'type',
        'mandatory',
        'choices',
        'default',
    ];

    protected $casts = [
        'mandatory' => 'boolean',
        'choices' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function myparcelShippingMethodService()
    {
        return $this->belongsTo(MyParcelShippingMethodService::class);
    }
}
