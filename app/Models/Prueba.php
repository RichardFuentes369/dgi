<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prueba extends Model
{
    // use SoftDeletes;
	protected $table = "prueba";

     protected $fillable = [
        'id',
        'name',
        'lastname',
        'email',
        'nickname',
    ];
}
