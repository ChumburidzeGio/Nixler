<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
	public $timestamps = false;

    public $table = 'metrics';

    protected $fillable  = [
        'key', 'value', 'object_type', 'object_id', 'date', 'target'
    ];
}