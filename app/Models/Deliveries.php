<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deliveries extends Model
{
    public $timestamps = true;
    protected $table = 'deliveries';
    protected $fillable = [
        'sender_name',
        'sender_address',
        'recipient_name',
        'recipient_address',
        'recipient_phone',
        'sender_phone',
        'volume',
        'status'
    ];
}
