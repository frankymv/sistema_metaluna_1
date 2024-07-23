<?php

namespace App\Livewire;


use App\Models\Vehiculo;
use App\Constantes\VehiculoData;
use Livewire\Component;

class VehiculoController extends Component
{



    public $title='Vehiculo';
    public $data, $id_data;
    public $isCreate = false,$isEdit = false, $isShow = false, $isDelete = false;
    public $estadoShow,$estadoFalse="Inactivo",$estadoTrue="Habilitado";
    public $created_at,$updated_at,$disabled=false;


    public $tipos=null,$placas=null,$marcas=null,$modelos=null, $estado=true;
    public $codigo=null, $tipo_vehiculo_id=null, $tipo_placa_id=null, $numero_placa=null, $marca_vehiculo_id=null, $modelo_vehiculo_id=null, $linea=null,$alias=null;

    protected $rules = [
        'codigo' => 'required',
        'tipo_vehiculo_id' => 'required',
        'tipo_placa_id' => 'required',
        'numero_placa' => 'required',
        'marca_vehiculo_id' => 'required',
        'modelo_vehiculo_id' => 'required',
    ];

    protected $listeners=['edit', 'delete','show'];

    public function render()
    {


        return view('livewire.pages.vehiculo.index');
    }

    public function create(){

        $this->modelos=VehiculoData::$modelo_vehiculo;
        $this->marcas=VehiculoData::$marca_vehiculo;
        $this->tipos=VehiculoData::$tipo_vehiculo;
        $this->placas=VehiculoData::$tipo_placa;



        $this->isCreate=true;
    }


    public function store(){

        $this->validate([
            'codigo' => 'required',
            'tipo_vehiculo_id' => 'required',
            'tipo_placa_id' => 'required',
            'numero_placa' => 'required',
            'marca_vehiculo_id' => 'required',
            'modelo_vehiculo_id' => 'required'
        ]);
        Vehiculo::create(
            [
            'codigo'=>$this->codigo,
            'tipo_vehiculo'=>$this->tipo_vehiculo_id,
            'tipo_placa'=>$this->tipo_placa_id,
            'numero_placa'=>$this->numero_placa,
            'marca'=>$this->marca_vehiculo_id,
            'modelo'=>$this->modelo_vehiculo_id,
            'linea'=>$this->linea,
            'alias'=>$this->alias,
            'estado'=>$this->estado,
            ]
        );

        $this->dispatch('pg:eventRefresh-default');
        $this->cancel();

    }

    public function edit($rowId){

        $this->modelos=VehiculoData::$modelo_vehiculo;
        $this->marcas=VehiculoData::$marca_vehiculo;
        $this->tipos=VehiculoData::$tipo_vehiculo;
        $this->placas=VehiculoData::$tipo_placa;



        $data = Vehiculo::find($rowId);
        $this->id_data=$data->id;
        $this->codigo=$data->codigo;
        $this->tipo_vehiculo_id=$data->tipo_vehiculo;
        $this->tipo_placa_id=$data->tipo_placa;
        $this->numero_placa=$data->numero_placa;
        $this->marca_vehiculo_id=$data->marca;
        $this->modelo_vehiculo_id=$data->modelo;
        $this->linea=$data->linea;
        $this->alias=$data->alias;
        $this->estado=$data->estado;

        $this->isEdit=true;


    }

    public function show($rowId){

        $this->modelos=VehiculoData::$modelo_vehiculo;
        $this->marcas=VehiculoData::$marca_vehiculo;
        $this->tipos=VehiculoData::$tipo_vehiculo;
        $this->placas=VehiculoData::$tipo_placa;

        $data = Vehiculo::find($rowId);
        $this->id_data=$data->id;
        $this->codigo=$data->codigo;
        $this->tipo_vehiculo_id=$data->tipo_vehiculo;
        $this->tipo_placa_id=$data->tipo_placa;
        $this->numero_placa=$data->numero_placa;
        $this->marca_vehiculo_id=$data->marca;
        $this->modelo_vehiculo_id=$data->modelo;
        $this->estado=$data->estado;
        $this->linea=$data->linea;
        $this->alias=$data->alias;




        $this->disabled=true;
        $this->isShow=true;
    }


    public function update($rowId){
        $this->validate([
            'codigo' => 'required',
            'tipo_vehiculo_id' => 'required',
            'tipo_placa_id' => 'required',
            'numero_placa' => 'required',
            'marca_vehiculo_id' => 'required',
            'modelo_vehiculo_id' => 'required'
        ]);

        $data = Vehiculo::find($rowId);
        $data->update([
            'codigo'=>$this->codigo,
            'tipo_vehiculo'=>$this->tipo_vehiculo_id,
            'tipo_placa'=>$this->tipo_placa_id,
            'numero_placa'=>$this->numero_placa,
            'marca'=>$this->marca_vehiculo_id,
            'modelo'=>$this->modelo_vehiculo_id,
            'linea'=>$this->linea,
            'alias'=>$this->alias,
            'estado'=>$this->estado,
        ]);


        $this->dispatch('pg:eventRefresh-default');
        $this->cancel();
    }

    public function delete($rowId){
        $data = Vehiculo::find($rowId);

        $this->id_data=$data->id;
        $this->alias=$data->alias;
        $this->isDelete = true;
    }

    public function destroy($rowId)
    {

        Vehiculo::find($rowId)->delete();
        $this->dispatch('pg:eventRefresh-default');
        $this->isDelete = false;
        //session()->flash('message', 'Post Deleted Successfully.');
        $this->cancel();
    }
    public function cancel(){
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields(){
        $this->reset(['isCreate','isEdit','isShow','isDelete','disabled','estado','created_at','updated_at']);
        ///////////////////
        $this->reset(['codigo','tipo_vehiculo_id','tipo_placa_id','numero_placa','marca_vehiculo_id','modelo_vehiculo_id','linea','alias','estado']);

        ////////////////////
    }


}
