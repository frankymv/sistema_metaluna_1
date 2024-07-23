<?php

namespace App\Livewire;

use App\Constantes\DataSistema;
use Constantes\DepartamentoMunicipio;
use App\Models\Envio;
use App\Models\EstadoEnvio;
use App\Models\Ruta;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Venta;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EnvioController extends Component
{
    public $title='Envio';
    public $data, $id_data,$id_last;
    public $isCreate = false,$isEdit = false, $isShow = false, $isDelete = false,$isFinalizar=false;
    public $estadoShow,$estadoFalse="Inactivo",$estadoTrue="Habilitado";
    public $created_at,$updated_at,$disabled=false,$disabled_observaciones_inicio_envio=false,$disabled_observaciones_final_envio=false;
    public $rutas=[], $municipios=[],$ventas=[],$vehiculos=[],$ventass=[];
    ////////////////


    public $i = 0;
    public $j = 0;
    public $k = 0;


    public $fecha;
    public $no_envio=0;

    public $usuarios=[];


    public $envio_id=null;

    public $venta_id=null;
    public $inputs=[];

    public $ventaDetalle=[];
    public $idDetalle=[];

    public $user_id=null;
    public $inputsUsuario=[];
    public $usuarioDetalle=[];
    public $idDetalleUsuario=[];


    public $vehiculo_id=null;
    public $inputsVehiculo=[];
    public $vehiculoDetalle=[];
    public $idDetalleVehiculo=[];

    public $estados,$procesos;

    public $observaciones_inicio_envio=null, $observaciones_final_envio=null,$estado='Iniciado';
    public $envios,$envio=null;


    public $envio_no=null;
    public $envio_fecha=null;

    public $ruta_id=null;

    public $proceso_id=null;
    public $proceso_nombre=null;

    public $estado_id=null;
    public $estado_nombre=null;
    public $estado_fecha=null;
    public $estado_observacion=null;

    public $user_id_created_at=null;
    public $user_name_created_at=null;

    public $visible=false;
    public $finalizado=false;

    public $disabled_envio_no=true;

public $disabled_venta=true,$disabled_user=true,$disabled_vehiculo=true;
public $diabled_proceso_id=false,$disabled_estado_id=false,$disabled_estado_obserbacion=false,$disabled_estado_fecha=false;

public $array = array(
   /* "clave1"  => "valor1",
    "clave2"  => "valor2",
    "clave3"  => "valor3",
    "clave4"  => "valor4",*/
);






    protected $listeners=['edit', 'delete','show','finalizar'];

    public function render()
    {


        $this->envios=Envio::find(1);
        return view('livewire.pages.envio.index');
    }

    public function create(){
        $this->diabled_proceso_id=true;
        $this->disabled_estado_id=false;
        $this->disabled_estado_obserbacion=false;
        $this->disabled_estado_fecha=true;

        $this->estados=ConstantesDataSistema::$estados;
        $this->procesos=DataSistema::$procesos;

        $this->proceso_id=1;
        $this->estado_id=3;

        $this->id_last=Envio::latest('id')->first();

        if ( $this->id_last===null) {
            $this->no_envio=0;
        }

        $this->usuarios=User::all();
        $this->ventas=Venta::where('envio',1)->where('en_ruta',false)->get();
        $this->rutas=Ruta::all();
        $this->vehiculos=Vehiculo::all();
        $this->isCreate=true;
    }



    public function borrador (){
        $this->proceso_id=1;
        $this->estado_id=3;
        $this->store();
    }

    public function addDetalleRuta(){
        foreach ($this->ventas as $key => $value) {
            if($value['id']===intval($this->venta_id)){
                array_push($this->inputs,$this->i);
                array_push($this->ventaDetalle,$value['no_venta']);
                array_push($this->idDetalle,$value['id']);
                $this->i +=1;
            }
        }
    }


    public function addDetalleUsuario(){
        foreach ($this->usuarios as $key => $value) {
            if($value['id']===intval($this->user_id)){
                array_push($this->inputsUsuario,$this->j);
                array_push($this->usuarioDetalle,$value['nombres']);
                array_push($this->idDetalleUsuario,$value['id']);
                $this->j +=1;
            }
        }
    }

    public function addDetalleVehiculo(){
        foreach ($this->vehiculos as $key => $value) {
            if($value['id']===intval($this->vehiculo_id)){

                array_push($this->inputsVehiculo,$this->k);
                array_push($this->vehiculoDetalle,$value['alias']);
                array_push($this->idDetalleVehiculo,$value['id']);
                $this->k +=1;
            }
        }
    }

    public function store(){
    $data=Envio::create(
        [
        'envio_no'=>$this->no_envio,
        'envio_fecha'=>$this->envio_fecha,
        'ruta_id'=>$this->ruta_id,
        'observaciones_inicio_envio'=>$this->observaciones_inicio_envio,
        'visible'=>'1',
        'finalizado'=>'0',
        ]);

        $data->ventas()->attach($this->idDetalle);
        $data->users()->attach($this->idDetalleUsuario);
        $data->vehiculos()->attach($this->idDetalleVehiculo);


        foreach ($this->idDetalle as $key => $value) {
            $data=Venta::find($value);
            $data->update([
                'en_ruta'=>True
            ]);


        }

    ////////////////////
        $this->dispatch('pg:eventRefresh-default');
        $this->cancel();
    }

    public function finalizar($id){

        $this->isFinalizar=true;
        $this->disabled=true;
        $this->disabled_observaciones_inicio_envio=true;
        $this->envio=Envio::where('id',$id['id'])->with('ventas')->with('vehiculos')->with('users')->first();

        //$this->estados=DataSistema::$estados;
        //$this->usuarios=User::all();
        //$this->ventas=Venta::where('envio',1)->get();
        //$this->rutas=Ruta::all();
        //$this->vehiculos=Vehiculo::all();

        foreach ($this->envio->ventas  as $key => $value) {
            $dataa=array("{$value['id']}"=>true);
            $this->array = array_merge($dataa,$this->array);
            //$this->array['clave4'];
        }

        $this->envio_id=$this->envio->id;
        $this->envio_no=$this->envio->envio_no;
        $this->envio_fecha=$this->envio->envio_fecha;
        $this->ruta_id=$this->envio->ruta_id;
        $this->created_at = $this->envio->created_at;
        $this->updated_at = $this->envio->updated_at;
        $this->observaciones_inicio_envio = $this->envio->observaciones_inicio_envio;
        ////////////////////
    }

    public function cancel(){
        $this->resetInputFields();
        $this->resetValidation();
    }

    public function store_finish(){
            ////////////////////
            $data = Envio::find($this->envio_id);
            $data->update([
                'observaciones_final_envio'=>$this->observaciones_final_envio,
                'finalizado'=>True
            ]);



            foreach ($data->ventas  as $key => $value) {

                $data = Venta::find($value['id']);
                $data->update([
                    'entregado'=>True
                ]);



            }


        ////////////////////
            $this->dispatch('pg:eventRefresh-default');
            $this->cancel();

    }

    private function resetInputFields(){
        $this->reset(['isCreate','isEdit','isShow','isFinalizar','isDelete','disabled','created_at','updated_at']);
        ///////////////////
        $this->reset(['no_envio','envio_id','fecha','estado','idDetalle','ventaDetalle','inputs','i','j','k',
        'diabled_proceso_id',
        'disabled_estado_id',
        'disabled_estado_obserbacion',
        'disabled_estado_fecha',
        ]);

        ////////////////////
    }


}
