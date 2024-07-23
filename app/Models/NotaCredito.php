<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    protected $fillable =['no_nota_credito','venta_id','cliente_id','total_venta','fecha_nota_credito','total_nota_credito','anulacion_venta','observaciones','correlativo','anulacion_venta','total_saldo'];
    use HasFactory;


    public function Venta(){
        return $this->belongsTo(Venta::class);
    }

}
