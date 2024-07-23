<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $fillable = ['codigo','tipo_vehiculo','tipo_placa','numero_placa','marca','modelo','linea','alias','estado'];

    /*public function Asignaciones(){
        return $this->hasMany(Asignacion::class);
    }

    */

    public function Asignacion(){
        return $this->belongsToMany(Asignacion::class);
    }

    public function Envio(){
        return $this->belongsToMany(Envio::class);
    }

    public function Servicios(){
        return $this->hasMany(Servicio::class);
    }

    protected function estado(): Attribute {
        return new Attribute(
            get: fn (string $value) => $value==='1' ? true:false,
            //  set: fn (string $value) => $value==='Activo'? true:false,

        );
    }
}
