<?php
namespace App\Livewire;

use App\Models\Compra;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Traslado;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
class TrasladoController extends Component
{
    public $title='Traslado';
    public $data, $id_data,$id_last;
    public $isCreate = false,$isEdit = false, $isShow = false, $isDelete = false;
    public $estadoShow,$estadoFalse="Inactivo",$estadoTrue="Habilitado";
    public $created_at,$updated_at,$disabled=false;
    /////////////
    public $proveedores,$nombre,$producto=[],$no_orden,$fecha,$proveedor_id;
    public $productoDetalle,$cantidadDetalle;
    public $inputs = [],$productos=[];
    public $detalleCompraMulti=[];
    public $nombresDetalle= [],$productosDetalle= [], $cantidadesDetalle= [];
    public $sucursals_origen=[],$sucursals_destino=[];
    public $i = 0;
    public $disabled_producto=false;
    public $disabled_existencia=true;
    public $disabled_cantidad=false;
    public $cantidad_existencia=0,$cantidad_transferir=0;
////////////////////
    public $traslado_no=null;
    public $traslado_fecha=null;
    public $producto_id=null;
    public $sucursal_origen_id=null;
    public $sucursal_destino_id=null;
    public $cantidad=0;
    public $sucur=null;
    public $produc=null;
    public $estado=true;

    public $disabledSucursalOrigen=false, $disabledSucursalDestino=false;

    public $id=null;
    protected $listeners=['edit', 'delete','show'];


    public function render()
    {


        return view('livewire.pages.traslado.index');
    }
    public function create(){

        $this->sucursal_origen_id=Auth::user()->sucursal_id;

        $this->disabledSucursalOrigen=true;



        $data=Traslado::latest()->first();

        if ( $data) {
            $this->id=$data->id+1;
            $this->traslado_no=$this->id;

        }else{
            $this->id=1;
            $this->traslado_no=$this->id;
        }

        $this->sucursals_origen=Sucursal::all();
        $this->sucursals_destino=Sucursal::all();
        $this->isCreate=true;
    }

    public function updatedSucursalDestinoId($value){





        if($this->sucursal_origen_id==$value)
        {
            $this->addError('sucursal_destino', 'No se puede realizar un traslado a la misma ubicacion');
            $this->productos=[];
        }else{
            $data=Sucursal::find(Auth::user()->sucursal_id);
            $this->sucur=$data->id;
            $this->productos=$data->Productos()->get();
        }


    }
/*
    public function updatedCantidadTransferir($value){
        $this->validate(['cantidad_transferir'=>"numeric|required|min:1|max:$this->cantidad_existencia"]);
    }
*/



    public function updatedProductoId ($values){
        $pro_sucu=DB::table('producto_sucursal')
        ->where('producto_id' ,'=', $values)
        ->where('sucursal_id','=', $this->sucursal_origen_id)
        ->first();
        $this->cantidad_existencia=$pro_sucu->cantidad;
    }

    /*

    public function updatedCantidadTransferir($value){
        if ($this->cantidad_producto>$this->existencia_producto) {
            $this->subtotal_producto=null;
            $this->addError('agregar_producto', 'La cantidad supera a la existencia actual');

        }else{
            $this->subtotal_producto=$value*$this->precio_venta_producto;
        };

    }

    */

    public function addDetalle(){

        $this->validate([
            'cantidad_transferir'=>"numeric|required|min:1|max:$this->cantidad_existencia"
        ]);

        if($this->cantidad_transferir>$this->cantidad_existencia){
            $this->addError('cantidad_transferir', 'La cantidad a trasladad no debe superar la existencia');
        }else{
        foreach ($this->productos as $key => $value) {
            if($value['id']===intval($this->producto_id)){
                array_push($this->inputs,$this->i);
                array_push($this->nombresDetalle ,$value['nombre']);
                array_push($this->productosDetalle ,$value['id']);
                array_push($this->cantidadesDetalle ,$this->cantidad_transferir);
                $this->i +=1;
            }
        }
        $this->reset(['producto_id','cantidad_transferir','cantidad_existencia']);
        $this->resetValidation();

        }
    }

    public function removeDetalle($i)
    {
        unset($this->inputs[$i]);
        unset($this->nombresDetalle[$i]);
        unset($this->productosDetalle[$i]);
        unset($this->cantidadesDetalle[$i]);
    }
    public function store(){
        $this->validate([
            'traslado_no' => 'required',
            'traslado_fecha' => 'required',
            'sucursal_origen_id' => 'required',
            'sucursal_destino_id' => 'required',
            'inputs'=>'required'
        ]);
        ////////////////////
        $data=Traslado::create(
            [
            'traslado_no'=>$this->traslado_no,
            'traslado_fecha'=>$this->traslado_fecha,
            'proveedor_id'=>$this->proveedor_id,
            'sucursal_origen_id'=>$this->sucursal_origen_id,
            'sucursal_destino_id'=>$this->sucursal_destino_id,
            'estado'=>$this->estado,
            ]
            );
            foreach ($this->productosDetalle as $key => $value) {


                if(DB::table('producto_sucursal')->where('producto_id',$value)->where('sucursal_id',$this->sucursal_destino_id)->exists()){

                    /////destinooo////
                    $pro_sucu_dest=DB::table('producto_sucursal')
                    ->where('producto_id' ,'=', $value)
                    ->where('sucursal_id','=', $this->sucursal_destino_id)
                    ->first();
                    $cant=$this->cantidadesDetalle[$key]+$pro_sucu_dest->cantidad;
                    DB::table('producto_sucursal')
                    ->where('producto_id' ,'=', $value)
                    ->where('sucursal_id','=', $this->sucursal_destino_id)
                    ->update(['cantidad' => $cant]);
                    ////origen////
                    $pro_sucu_org=DB::table('producto_sucursal')
                    ->where('producto_id' ,'=', $value)
                    ->where('sucursal_id','=', $this->sucursal_origen_id)
                    ->first();
                    $cant=$pro_sucu_org->cantidad-$this->cantidadesDetalle[$key];
                    DB::table('producto_sucursal')
                    ->where('producto_id' ,'=', $value)
                    ->where('sucursal_id','=', $this->sucursal_origen_id)
                    ->update(['cantidad' => $cant]);





                }else{
                    ////destino////
                    DB::table('producto_sucursal')->insert([
                        'producto_id' => $value,
                        'sucursal_id' => $this->sucursal_destino_id,
                        'cantidad' =>$this->cantidadesDetalle[$key]
                    ]);

                    ////origen////
                    $pro_sucu_org=DB::table('producto_sucursal')
                    ->where('producto_id' ,'=', $value)
                    ->where('sucursal_id','=', $this->sucursal_origen_id)
                    ->first();
                    $cant=$pro_sucu_org->cantidad-$this->cantidadesDetalle[$key];
                    DB::table('producto_sucursal')
                    ->where('producto_id' ,'=', $value)
                    ->where('sucursal_id','=', $this->sucursal_origen_id)
                    ->update(['cantidad' => $cant]);


                }


                $data->productos()->attach($value,['cantidad' => $this->cantidadesDetalle[$key]]);

                $da=DB::table('producto_sucursal')
                    ->where('producto_id' ,'=', $value)
                    ->sum('cantidad');


                Producto::find($value)
                        ->update(['existencia' => $da]);



            }
























        ////////////////////
        $this->dispatch('pg:eventRefresh-default');
        $this->cancel();
    }
    public function edit($rowId){
        ////////////////////

        ////////////////////
    }

    public function show($rowId){
        ////////////////////

        ////////////////////
    }
    public function delete($rowId){
        $data = Traslado::find($rowId);
        $this->traslado_no=$data->traslado_no;
        $this->id_data=$data->id;
        $this->traslado_no = $data->traslado_no;
        $this->isDelete = true;
}

    public function destroy($rowId){
        $data=Traslado::find($rowId);
        $datos=$data->productos()->get();
        foreach ($datos as $key => $value) {
            array_push($this->inputs ,$key);
            array_push($this->nombresDetalle ,$value->nombre);
            array_push($this->productosDetalle ,$value->id);
            array_push($this->cantidadesDetalle ,$value->pivot->cantidad);
        }

        foreach ($this->productosDetalle as $key => $value) {


            if(DB::table('producto_sucursal')->where('producto_id',$value)->where('sucursal_id',$data->sucursal_origen_id)->exists()){



                /////destinooo////
                $pro_sucu_ori=DB::table('producto_sucursal')
                ->where('producto_id' ,'=', $value)
                ->where('sucursal_id','=', $data->sucursal_destino_id)
                ->first();

                $cant=$pro_sucu_ori->cantidad-$this->cantidadesDetalle[$key];
                DB::table('producto_sucursal')
                ->where('producto_id' ,'=', $value)
                ->where('sucursal_id','=', $data->sucursal_destino_id)
                ->update(['cantidad' => $cant]);
                ////origen////

                $pro_sucu_dest=DB::table('producto_sucursal')
                ->where('producto_id' ,'=', $value)
                ->where('sucursal_id','=', $data->sucursal_origen_id)
                ->first();
                $cant=$pro_sucu_dest->cantidad+$this->cantidadesDetalle[$key];
                DB::table('producto_sucursal')
                ->where('producto_id' ,'=', $value)
                ->where('sucursal_id','=', $data->sucursal_origen_id)
                ->update(['cantidad' => $cant]);
            }


            $data->productos()->detach($value,['cantidad' => $this->cantidadesDetalle[$key]]);


        }
        $data->delete();

        $this->dispatch('pg:eventRefresh-default');
        $this->cancel();
    }








    public function cancel(){
        $this->resetInputFields();
        $this->resetValidation();
    }
    private function resetInputFields(){
        $this->reset(['isCreate','isEdit','isShow','isDelete','disabled','created_at','updated_at']);
        ///////////////////
        $this->reset(['no_orden','traslado_fecha','sucursal_origen_id','sucursal_destino_id','producto_id','cantidad_existencia','cantidad_transferir','cantidad','estado','nombresDetalle','productosDetalle','cantidadesDetalle','producto','inputs','i']);
        ////////////////////
    }
}
