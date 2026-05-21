<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objeto extends Model
{
    public function compras()
    {
        return $this->belongsToMany(Compra::class, 'compra_objeto', 'idObjeto', 'idCompra');
    }

    public function alquileres()
    {
        return $this->belongsToMany(Alquilere::class, 'alquiler_objeto', 'idObjeto', 'idAlquiler');
    }
}
