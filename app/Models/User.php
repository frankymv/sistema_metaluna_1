<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /*frk origina
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    */

    protected $fillable = [
        'name',
        'codigo',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'cui',
        'telefono_principal',
        'telefono_secundario',
        'tipo_sangre',
        'no_licencia',
        'inicio_labores',
        'fin_labores',
        'direccion_fisica',
        'direccion_departamento',
        'direccion_municipio',
        'usuario',
        'sucursal_id',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function Asignacion(){
        return $this->belongsToMany(Asignacion::class);
    }

    public function Envio(){
        return $this->belongsToMany(Envio::class);
    }

    public function Sucursal(){
        return $this->belongsTo(Sucursal::class);
    }
    protected function estado(): Attribute {
        return new Attribute(
            get: fn (string $value) => $value==='1' ? true:false,
            //  set: fn (string $value) => $value==='Activo'? true:false,

        );
    }
}
