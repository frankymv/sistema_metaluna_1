<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\EstadoCuenta;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Tipo;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class VentaController extends Component
{
    public $title='Detalle Venta';
    public $data, $id_data,$id_last;
    public $isCreate = false,$isEdit = false, $isShow = false, $isDelete = false,$isAddProduct=false,$disabled_nombre_producto=false,$disabled_existencia_producto=false,$disabled_codigo_producto=false,$disabled_subtotal_producto=false,$tipo_cliente;
    public $disabledInput=false,$disabledInputPasswordAdmin=false;


    public $estadoShow,$estadoFalse="Inactivo",$estadoTrue="Habilitado";
    public $created_at,$updated_at,$disabled=false,$abono_venta;

    /////////////
    public $codigo='',$proveedores,$productos=[],$nombre,$producto,$producto_id,$cantidad,$no_orden,$fecha,$proveedor_id,$tipo_id=0,$estado=true,$buscar_producto,$buscar_cliente;
    public $productoDetalle,$cantidadDetalle,$tipos,$total_venta;
    public $inputs = [];
    public $detalleCompraMulti=[],$tipo_venta=[['id'=>'0','nombre'=>'Cotizacion'],['id'=>'1','nombre'=>'Efectivo'],['id'=>'2','nombre'=>'Credito'],['id'=>'3','nombre'=>'Efectivo / Credito']];
    public $tipo_pago=[['id'=>'0','nombre'=>'contado'],['id'=>'1','nombre'=>'credito'],['id'=>'2','nombre'=>'abono']];
    public $nombresDetalle= [],$productosDetalle= [], $cantidadesDetalle= [],$subtotalDetalle= [],$clientes=[],$total=0;
    public $i = 0;

    public $no_venta=null;
    public $fecha_venta=null;
    public $cantidad_efectivo=0,$cantidad_credito=0,$cancelado=false,$cliente_id;

    public $id_producto,$codigo_producto,$existencia_producto=1,$nombre_producto='',$precio_venta_producto=0,$cantidad_producto=0,$subtotal_producto=0,$precio_venta_base=0;
    public $disabled_precio_venta_producto=false,$disabled_cantidad_producto=false, $disabledCredito=true;

    public $id_cliente,$id_codigo, $nombre_empresa, $nombres_cliente, $apellidos_cliente, $nit, $descuento=0, $total_descuento=0,$ahorro=0, $direccion_fisica,$direccion_departamento, $direccion_municipio;
    public $usuario_edit,$codigo_edit;
    public $envio=false,$venta=null;


    protected $listeners=['edit', 'delete','showDetalle','pdfExportar'];

    public function render()
    {
        return view('livewire.pages.venta.index');
    }

    public function showDetalle($value){
        $this->isShow=true;
        $this->disabledInput=true;
        $this->venta=Venta::where('no_venta',$value['no_venta'])->with('cliente')->with('productos')->first();

       // dd($this->venta);
        $this->codigo=$this->venta->cliente->codigo;
        $this->nit=$this->venta->cliente->nit;
        $this->nombres_cliente=$this->venta->cliente->nombres_cliente;
        $this->nombre_empresa=$this->venta->cliente->nombre_empresa;
        $this->direccion_fisica=$this->venta->cliente->direccion_fisica;
        $this->direccion_departamento=$this->venta->cliente->direccion_departamento;
        $this->direccion_municipio=$this->venta->cliente->direccion_municipio;
        $this->tipo_cliente=$this->venta->cliente->tipo_cliente;
        $this->no_venta=$this->venta->no_venta;
        $this->fecha_venta=$this->venta->fecha_venta;
        $this->total_venta=$this->venta->total_venta;
        $this->cliente_id=$this->venta->cliente_id;



    }
    public function pdfExportar($id){

        return redirect()->route('pdfExportarVenta',$id);

    }

    public function pdfExportarVenta($id)
    {
        $saldo_actual=0;
        $saldo_anterior=0;

        $venta=Venta::with('productos')->find($id)->toArray();

        $no_venta=$venta['no_venta'];



        $cliente=Cliente::find($venta['cliente_id'])->toArray();

        //$user=User::find(1)->toArray();
        $saldo_anterior=$venta['saldo_credito'];

        if ($venta['forma_pago']==='CREDI') {
            $data=EstadoCuenta::where('cliente_id','=',$venta['cliente_id'])->get();

            $saldo_actual=$saldo_anterior+$venta['total_venta'];
        }else{
            $saldo_anterior=0;
            $saldo_actual=$venta['total_venta'];
        }

        $pdf = FacadePdf::loadView('/livewire/pdf/pdfVentaRapida',['venta' => $venta,'cliente'=>$cliente,'saldo_anterior'=>$saldo_anterior,'saldo_actual'=>$saldo_actual]);



        //$pdf = Pdf::loadView('pdf.invoice', $data);
        return $pdf->download("venta_$no_venta.pdf");

        //return redirect()->route('pdfVentaRapida',$id);

        //return $pdf->download('venta_pdf.pdf');
        //return $pdf->stream();
        //return $pdf->download('itsolutionstuff.pdf');

    }


    private function resetInputFields(){
        $this->reset(['isCreate','isEdit','isShow','isDelete','disabled','created_at','updated_at']);
        ///////////////////
        $this->reset(['productos','no_orden','fecha','cantidad','estado','nombresDetalle','productosDetalle','cantidadesDetalle','producto','inputs','clientes']);
        ////////////////////
    }


    public function cancel(){
        $this->resetInputFields();
        $this->resetValidation();

    }
}

