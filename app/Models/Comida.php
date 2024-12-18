<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comida extends Model
{
    protected $table = 'comida';

    protected $fillable  =  [
        'nombre',
        'ingredientes',
        'categoria',
        'precio',
        'detalles'
    ];

}
