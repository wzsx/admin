<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SmsLogModel extends Model
{
    //
    protected $fillable = [
        'type',
        'mobile',
        'code',
        'checked',
        'status',
        'reason',
        'remark',
        'operator_id',
        'ip',
    ];
    public $table = 'sms_logs';
    public $timestamps = false;
}
