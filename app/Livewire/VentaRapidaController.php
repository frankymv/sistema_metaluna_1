<?php

namespace App\Livewire;

use App\Constantes\DataSistema;
use App\Models\Abono;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Credito;
use App\Models\EstadoCuenta;
use App\Models\Marca;
use App\Models\Material;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Tipo;
use App\Models\User;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;
//use Dompdf\Adapter\PDFLib;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class VentaRapidaController extends Component
{
    use LivewireAlert;
    ///sistema
    public $title='Venta';
    public $data, $id_data,$ultima_venta,$id=null;
    public $isCreate=false, $isAddProduct=false, $isSearchProduct=false, $isVentaDetalle=false;

    ////venta
    public $no_venta=null,$fecha_venta=null, $total_venta=0,$observaciones_venta=null,$forma_pago=null,$saldo_venta=0;
    //cliente
    public $cliente_id=null,$codigo=null, $nombre_empresa=null,$nombres_cliente=null, $apellidos_cliente=null, $tipo_cliente=null, $nit=null,$descuento=0,$direccion_fisica=null,$direccion_departamento=null,$direccion_municipio=null;
    //efectivo
    public $efectivo=false;
    //credito
    public $credito=false, $total_credito=0,$observaciones_credito='';
    //anuladoooo
    public $anulado=false, $fecha_anulado=null, $observaciones_anulado='';
    //nota de credito
    public $nota_credito=false, $no_credito=null,$total_nota_credito=0, $fecha_nota_credito=null,$observaciones_nota_credito='';
    //cancelado
    public $cancelado=false, $fecha_cancelado=null;
    ///statics
    public $envios=[],$forma_pagos=[],$tipos=[],$productos=[],$marcas=[],$materiales=[];
    public $nombresDetalle= [],$productosDetalle= [], $cantidadesDetalle= [],$subtotalDetalle= [],$total=0;
    //consultas
    public $proveedores=null;
    ///inputs bloqueo
    public $disabledInput=false,$disabled=false;
    public $dias_ultimo_credito=0;


    ///variables agregar cantidad
    //inputs bloqueo agregar cantidad
    public $disabled_precio_venta_producto=false;
    public $disabled_cantidad_producto=false;
    public $disabled_nombre_producto=false;
    public $disabled_existencia_producto=false;
    public $disabled_codigo_producto=false;
    public $disabled_subtotal_producto=false;

    public $precio_venta_producto=0,$cantidad_producto=0, $subtotal_producto=0;


    ////productooo

    public $id_producto=null;
    public $codigo_producto=null;
    public $nombre_producto=null;
    public $existencia_producto=null;
    public $precio_venta_base=null;

    public $temp=null;

    public $limite_credito_temp=null;


    //variables
    public $id_forma_pago=null, $id_envio=null, $id_tipo=null, $id_marca=null, $id_material=null;


    public $disabledInputPasswordAdmin=null;

    //form
    public $buscar_nit='',$saldo_credito=0,$nuevo_saldo=0,$buscar_producto=null;

    //usuario para liberar credito
    public $liberar_credito_password=null;
    public $liberar_credito_usuario=null;
    public $autorizacion_limite_credito=false;

    protected $listeners=['edit', 'delete','show'];

    public $abono_anticipado=0;
//////////////liberar o desbloquear precio////

public $email_edit=null, $codigo_edit=null;

    /////////buscar cliente////////////
    public $isSearchCliente=false;
    public $clientes=[];
    public $search_nombres_cliente=null;
    public $search_codigo_cliente=null;
    public $search_nit_cliente=null;
    //////////////INDEX/////////////////

    public function render(){




        $this->temp = Venta::all();
        $this->forma_pagos=DataSistema::$forma_pago;
        $data=Venta::latest()->first();


        if ( $data) {
            $this->id=$data->id+1;
            $this->no_venta=$this->id;

        }else{
            $this->id=1;
            $this->no_venta=$this->id;
        }
        $this->proveedores=Proveedor::all();
        $this->fecha_venta = Carbon::now()->toDateString();
        $this->disabledInput=true;
        return view('livewire.pages.venta_rapida.index');
    }



    public function searchCliente(){
        $this->isSearchCliente=true;
    }





    public function updatedSearchNombresCliente($value){
        $this->reset(['search_codigo_cliente','search_nit_cliente']);
        $this->clientes=Cliente::where('nombres_cliente','like',"%$value%")->get();
    }

    public function updatedSearchCodigoCliente($value){
        $this->reset(['search_nombres_cliente','search_nit_cliente']);
        $this->clientes=Cliente::where('codigo_mayorista','like',"%$value%")->get();

    }
    public function updatedSearchNitCliente($value){
        $this->reset(['search_nombres_cliente','search_codigo_cliente']);
        $this->clientes=Cliente::where('nit','like',"%$value%")->get();

    }

    public function agregarCliente($id){
            $cliente=Cliente::find($id);
            $this->cliente_id= $cliente->id;
            $this->codigo= $cliente->codigo;
            $this->nombre_empresa= $cliente->nombre_empresa;
            $this->nombres_cliente= $cliente->nombres_cliente;
            $this->apellidos_cliente= $cliente->apellidos_Cliente;
            $this->nit= $cliente->nit;
            $this->descuento= $cliente->descuento;
            $this->direccion_fisica= $cliente->direccion_fisica;
            $this->direccion_departamento= $cliente->direccion_departamento;
            $this->direccion_municipio= $cliente->direccion_municipio;


            if ($cliente->tipo_cliente!=1) {
                $this->tipo_cliente='MAY';
            }else{
                $this->tipo_cliente='MIN';
            }

            $credito=EstadoCuenta::where('cliente_id','=',$id)->first();
            if($credito){
                $this->saldo_credito=$credito->total_credito-$credito->total_saldo;

            $dataaa=DB::table('creditos')
            ->select('fecha_credito')
            ->where('cliente_id',2)
            ->orderBy('fecha_credito', 'asc')
            ->first();
            $fechaPrimerCredito = Carbon::parse($dataaa->fecha_credito);



            $fechaActual = Carbon::now();


            $client=Cliente::where('nit','=',$id)->first();

            $this->dias_ultimo_credito=(int) round($client->dias_limite_credito-$fechaPrimerCredito->diffInDays($fechaActual));

            }else{
                $this->saldo_credito=0;
            }


               $data=Abono::where('cliente_id','=',$id)->first();
                if(!$data){
                    $this->abono_anticipado=0;
                }else{
                    $this->abono_anticipado=$data->total_abono;
                }

                $this->reset(['isSearchCliente','search_nombres_cliente','search_codigo_cliente','search_nit_cliente','clientes']);


    }

    public function cancelarBuscarCliente(){
        $this->reset(['isSearchCliente','search_nombres_cliente','search_codigo_cliente','search_nit_cliente','clientes']);
    }


    public function buscarCliente(){
        $this->validate(['buscar_nit'=>'numeric|required|min:00000|max:99999']);
        if( $this->buscar_nit==='00000' || $this->buscar_nit===''){
            $this->cliente_id=1;
            $this->nit='c/f';
            $this->codigo='---';
            $this->tipo_cliente='MIN';
            $this->nombres_cliente= "Consumidor Final";
            $this->apellidos_cliente= "";
            $this->direccion_fisica= "Ciudad";

            $this->alert('success', 'Cliente C/F', [
                'position' => 'center',
                'timer' => '2000',
                'toast' => true,
                'showConfirmButton' => false,
                'onConfirmed' => '',
                'timerProgressBar' => true,
               ]);
        }elseif($cliente=Cliente::where('nit','=',$this->buscar_nit)->first()){
            $this->cliente_id= $cliente->id;
            $this->codigo= $cliente->codigo;
            $this->nombre_empresa= $cliente->nombre_empresa;
            $this->nombres_cliente= $cliente->nombres_cliente;
            $this->apellidos_cliente= $cliente->apellidos_Cliente;
            $this->nit= $cliente->nit;
            $this->descuento= $cliente->descuento;
            $this->direccion_fisica= $cliente->direccion_fisica;
            $this->direccion_departamento= $cliente->direccion_departamento;
            $this->direccion_municipio= $cliente->direccion_municipio;


            if ($cliente->tipo_cliente!=1) {
                $this->tipo_cliente='MAY';
            }else{
                $this->tipo_cliente='MIN';
            }

            $credito=EstadoCuenta::where('cliente_id','=',$cliente->id)->first();
            if($credito){
                $this->saldo_credito=$credito->total_credito-$credito->total_saldo;

            $dataaa=DB::table('creditos')
            ->select('fecha_credito')
            ->where('cliente_id',2)
            ->orderBy('fecha_credito', 'asc')
            ->first();
            $fechaPrimerCredito = Carbon::parse($dataaa->fecha_credito);



            $fechaActual = Carbon::now();


            $client=Cliente::where('nit','=',$this->buscar_nit)->first();

            $this->dias_ultimo_credito=(int) round($client->dias_limite_credito-$fechaPrimerCredito->diffInDays($fechaActual));

            }else{
                $this->saldo_credito=0;
            }
            $this->alert('success', 'Cliente encontrado', [
                'position' => 'center',
                'timer' => '3000',
                'toast' => true,
                'showConfirmButton' => true,
                'onConfirmed' => '',
                'timerProgressBar' => true,
               ]);

               $data=Abono::where('cliente_id','=',$this->cliente_id)->first();
                if(!$data){
                    $this->abono_anticipado=0;
                }else{
                    $this->abono_anticipado=$data->total_abono;
                }
        }else{

            $this->reset([
                'cliente_id',
                'nit',
                'codigo',
                'tipo_cliente',
                'nombres_cliente',
                'apellidos_cliente',
                'direccion_fisica'
            ]);
            $this->alert('success', 'Cliente No encontrado', [
                'position' => 'center',
                'timer' => '2000',
                'toast' => true,
                'showConfirmButton' => false,
                'onConfirmed' => '',
                'timerProgressBar' => true,
               ]);
        }
    }

    public function updatedIdFormaPago($value){

        $this->reset('no_credito');

        if($value==='CREDI'){

            if($data=Credito::latest()->first()){
                $data=Credito::latest()->first();
                $this->no_credito=$data->id+1;
            }else{
                $this->no_credito=1;
            }
        }else{
            $this->no_credito=0;
        }





    }


    //////////////// BUSCAR PRODUCTO////////////////////

    public function buscarProducto(){
        //$this->bandera=$this->bandera+1;
        $this->tipos=Tipo::all();
        $this->marcas=Marca::all();
        $this->materiales=Material::all();
        $this->reset(['productos','id_tipo']);
        $this->isSearchProduct=true;

    }

    public function updatedBuscarProducto($value){
        $this->reset(['productos','id_tipo','id_marca','id_material']);
        $this->productos=Producto::where('nombre','LIKE',"%{$value}%")->get();
    }


    public function updatedIdTipo($value){
        $this->reset(['buscar_producto','productos','id_marca','id_material']);
        $this->productos=Producto::query()
        ->where('tipo_id',$value)
        ->get();
    }
    public function updatedIdMarca($value){
        $this->reset(['buscar_producto','productos','id_tipo','id_material']);
        $this->productos=Producto::query()
        ->where('marca_id',$value)
        ->get();
    }
    public function updatedIdMaterial($value){
        $this->reset(['buscar_producto','productos','id_tipo','id_marca']);
        $this->productos=Producto::query()
        ->where('material_id',$value)
        ->get();
    }

    public function cancelBuscarProducto(){
        $this->reset(['buscar_producto','productos','id_tipo','id_marca','isSearchProduct']);
        $this->resetValidation();
    }

        //////////////// AGREGAR CANTIDAD PRODUCTO////////////////////

    public function agregarCantidadProducto($id){

        $this->reset(['buscar_producto','productos','id_tipo','id_marca','isSearchProduct']);

        $this->productos=Producto::find($id);
        $this->disabled_precio_venta_producto=true;
        $this->disabled_cantidad_producto=false;
        $this->disabled_nombre_producto=true;
        $this->disabled_existencia_producto=true;
        $this->disabled_codigo_producto=true;
        $this->disabled_subtotal_producto=true;

        $this->isAddProduct=true;

        $this->id_producto=$this->productos->id;
        $this->codigo_producto=$this->productos->codigo;
        $this->nombre_producto=$this->productos->nombre;
        $this->existencia_producto=$this->productos->existencia;
        $this->precio_venta_base=$this->productos->precio_venta_base;

        if ($this->tipo_cliente==='MAY') {
            $this->precio_venta_producto=$this->productos->precio_venta_base;
        }else {
            $this->precio_venta_producto=$this->productos->precio_venta_base;
        }
    }

    public function unlock(){


        if(User::where('email',$this->email_edit)->where('codigo', $this->codigo_edit)->exists()){

            $this->disabled_precio_venta_producto=false;
            $this->alert('success', 'Precio desbloqueado', [
                'position' => 'center',
                'timer' => '2000',
                'toast' => true,
                'showConfirmButton' => false,
                'onConfirmed' => '',
                'timerProgressBar' => true,
               ]);
        }else{
            $this->alert('error', 'Usuario Incorrecto', [
                'position' => 'center',
                'timer' => '2000',
                'toast' => true,
                'showConfirmButton' => false,
                'onConfirmed' => '',
                'timerProgressBar' => true,
               ]);
            $this->disabled_precio_venta_producto=true;
        }
    }

    public function actualizarPrecio(){
        if(!$this->disabledInputPasswordAdmin)
        {
            $this->disabledInputPasswordAdmin=true;
        }else{
            $this->disabledInputPasswordAdmin=false;
        }
    }

    public function updatedCantidadProducto($value){

        $this->validate(['cantidad_producto'=>"numeric|required|min:1|max:$this->existencia_producto"]);
        if(!$value){
            $value=0;
        }
        if ($this->cantidad_producto>$this->existencia_producto) {
            $this->subtotal_producto=null;
            $this->addError('agregar_producto', 'La cantidad supera a la existencia actual');

        }else{
            $this->subtotal_producto=$value*$this->precio_venta_producto;
        };
    }

    public function agregarDetalle($id){
        $this->validate(['subtotal_producto'=>'required',
    'cantidad_producto'=>"numeric|required|min:1|max:$this->existencia_producto"]);

        $this->productos=Producto::query()
        ->where('id','=',$id)
        ->get();

        $datatempproducto=[];
        foreach ($this->productos as $key => $value) {
            if($value['id']===intval($this->id_producto)){
                $datatempproducto=$value->attributesToArray();
                $datatempproducto+=['precio_venta_producto'=>$this->precio_venta_producto];
                $datatempproducto+=['cantidad_producto'=>$this->cantidad_producto];
                $datatempproducto+=['subtotal_producto'=>$this->subtotal_producto];
                array_push($this->productosDetalle,$datatempproducto);
                $this->total_venta=$this->total_venta+$this->subtotal_producto;
                $this->nuevo_saldo=$this->saldo_credito+$this->total_venta;
            }
        }

        $this->cancelProductQuantity();
        $this->alert('success', 'Producto Agregado', [
            'position' => 'center',
            'timer' => '2000',
            'toast' => true,
            'showConfirmButton' => false,
            'onConfirmed' => '',
            'timerProgressBar' => true,
           ]);
    }

    public function removeDetalle($i){
        $this->total_venta=$this->total_venta-$this->productosDetalle[$i]['subtotal_producto'];
        $this->total_venta=$this->total_venta+$this->subtotal_producto;
        unset($this->productosDetalle[$i]);
    }


    public function store(){
        $this->validate(['id_forma_pago'=>'required','nombres_cliente'=>'required']);
        if($this->nit!=null){
            $cliente=Cliente::find($this->cliente_id);
            $this->limite_credito_temp=$cliente->limite_credito;
        }
        $data=null;

        $id=0;
        $credito=0;
        $saldo_venta=0;

        if ($this->productosDetalle!=[]) {
            if ($this->id_forma_pago==="CREDI") {
                if(EstadoCuenta::where('cliente_id',$this->cliente_id)->exists()){
                    $data_estado_cuenta=EstadoCuenta::where('cliente_id',$this->cliente_id)->first();

                    $cantidad_credito=($data_estado_cuenta->total_credito-$data_estado_cuenta->total_abono)+$this->total_venta;
                }else{
                    $cantidad_credito=$this->total_venta;
                }
                if($cantidad_credito>=$this->limite_credito_temp && !$this->autorizacion_limite_credito){
                    $this->addError('saldo_credito', 'Supera el credito asignado');
                }else{
                    $credito=$this->total_venta;
                    $saldo_venta=$this->total_venta;
                    $data=Venta::create(
                        [
                            'cliente_id'=>$this->cliente_id,
                            'no_venta'=>$this->no_venta,
                            'fecha_venta'=>$this->fecha_venta,
                            'hora_venta'=>Carbon::now()->toTimeString(),
                            'total_venta'=>$this->total_venta,
                            'observaciones_venta'=>$this->observaciones_venta,
                            'saldo_credito'=>$this->saldo_credito,

                            'sucursal_id'=>Auth::user()->sucursal_id,
                            'forma_pago'=>$this->id_forma_pago,

                            'credito'=>true,
                            'no_credito'=>$this->no_credito,
                            'total_credito'=>$credito,
                            'observaciones_credito'=>$this->observaciones_credito,
                            'saldo_venta'=>$saldo_venta
                        ]);

                    $id=$data->id;
                foreach ($this->productosDetalle as $key => $value) {
                    $data->productos()->attach($value['id'],['cantidad' => $value['cantidad_producto'],'precio_venta' => $value['precio_venta_producto'],'sub_total' => $value['subtotal_producto']]);
                }
                $this->alert('success', "Venta Realizada: $this->no_venta ", [
                    'position' => 'center',
                    'timer' => '3000',
                    'toast' => true,
                    'showConfirmButton' => false,
                    'onConfirmed' => '',
                    'timerProgressBar' => true,
                   ]);
                   return redirect()->route('pdfVentaRapida',$id);
                }
            }

            if ($this->id_forma_pago==="EFECT") {
                $saldo_venta=0;
                $data=Venta::create(
                    [
                        'cliente_id'=>$this->cliente_id,

                        'no_venta'=>$this->no_venta,
                        'fecha_venta'=>$this->fecha_venta,
                        'hora_venta'=>Carbon::now()->toTimeString(),
                        'total_venta'=>$this->total_venta,
                        'observaciones_venta'=>$this->observaciones_venta,
                        'saldo_credito'=>$this->saldo_credito,

                        'sucursal_id'=>Auth::user()->sucursal_id,
                        'forma_pago'=>$this->id_forma_pago,

                        'efectivo'=>true,
                        'cancelado'=>true,
                        'fecha_cancelado'=>$this->fecha_venta,
                        'saldo_venta'=>$saldo_venta,

                        'credito'=>false,
                        'no_credito'=>'0',
                        'total_credito'=>'0',
                        'observaciones_credito'=>'',
                    ]);


                    $id=$data->id;
                    foreach ($this->productosDetalle as $key => $value) {
                        $data->productos()->attach($value['id'],['cantidad' => $value['cantidad_producto'],'precio_venta' => $value['precio_venta_producto'],'sub_total' => $value['subtotal_producto']]);
                    }
                    $this->alert('success', "Venta Realizada: $this->no_venta ", [
                        'position' => 'center',
                        'timer' => '3000',
                        'toast' => true,
                        'showConfirmButton' => false,
                        'onConfirmed' => '',
                        'timerProgressBar' => true,
                       ]);
                       return redirect()->route('pdfVentaRapida',$id);
                }
        }else {
            $this->alert('error', 'Error', [
                'position' => 'center',
                'timer' => '2000',
                'toast' => true,
                'showConfirmButton' => false,
                'onConfirmed' => '',
                'timerProgressBar' => true,
                'text' => 'Debe agregar producto',
            ]);
        }
    }



    public function liberarCredito(){



        if(User::where('email',$this->email_edit)->where('codigo', $this->codigo_edit)->exists()){
            $this->autorizacion_limite_credito=true;
            $this->alert('success', "Limite autorizado", [
                'position' => 'center',
                'timer' => '3000',
                'toast' => true,
                'showConfirmButton' => false,
                'onConfirmed' => '',
                'timerProgressBar' => true,
               ]);
        }else{
            $this->alert('error', "Limite No autorizado", [
                'position' => 'center',
                'timer' => '3000',
                'toast' => true,
                'showConfirmButton' => false,
                'onConfirmed' => '',
                'timerProgressBar' => true,
               ]);

        }
    }

////////////////////////////PDF//////////////////////////
    public function pdfVentaRapida($id)
    {
        $saldo_actual=0;
        $saldo_anterior=0;
        $venta=Venta::with('productos')->find($id)->toArray();
        $no_venta=$venta['no_venta'];
        $cliente=Cliente::find($venta['cliente_id'])->toArray();
        $saldo_anterior=$venta['saldo_credito'];

        if(EstadoCuenta::where('cliente_id','=',$venta['cliente_id'])->exists()){
            $data=EstadoCuenta::where('cliente_id','=',$venta['cliente_id'])->get();

            if ($venta['forma_pago']==="CREDI") {
                $saldo_actual=$saldo_anterior+$venta['total_venta'];
                $saldo_anterior=$venta['saldo_credito'];
            }else{
                $saldo_actual=$venta['total_venta'];
                $saldo_anterior=$venta['saldo_credito'];
            }
        }else{
            $saldo_anterior=0;
            $saldo_actual=$venta['total_venta'];
        }

        $pdf = FacadePdf::loadView('/livewire/pdf/pdfVentaRapida',['venta' => $venta,'cliente'=>$cliente,'saldo_anterior'=>$saldo_anterior,'saldo_actual'=>$saldo_actual]);

        //$pdf = Pdf::loadView('pdf.invoice', $data);
       // return $pdf->download("venta_$no_venta.pdf");

        //return redirect()->route('pdfVentaRapida',$id);

        //return $pdf->download('venta_pdf.pdf');
        return $pdf->stream();
        //return $pdf->download('itsolutionstuff.pdf');

    }


    public function cancelarBuscarProducto(){
        $this->reset(['isSearchProduct','buscar_producto','id_tipo','tipos','marcas','id_marca','materiales','id_material','productos']);
        $this->resetValidation();
    }


    public function cancelProductQuantity(){
        $this->reset(['email_edit','codigo_edit']);
        $this->reset(['isAddProduct','codigo_producto','nombre_producto','existencia_producto','precio_venta_producto','cantidad_producto','subtotal_producto']);
        $this->resetValidation();
    }

    public function borrarTodo(){
        $this->reset();
        $this->alert('error', 'Datos Borrados', [
            'position' => 'center',
            'timer' => '2000',
            'toast' => true,
            'showConfirmButton' => false,
            'onConfirmed' => '',
            'timerProgressBar' => true,
            'text' => 'Datos borrados correctamente',
           ]);
    }
}

