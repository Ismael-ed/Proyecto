<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model {
    protected $table = 'compras';
    protected $primaryKey = 'idCompra';
    protected $guarded = [];
}
