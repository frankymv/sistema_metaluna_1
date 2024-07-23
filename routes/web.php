<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Livewire\AbonoController;
use App\Livewire\MarcaController;
use App\Livewire\InicioController;
use App\Livewire\AjusteInventarioController;
use App\Livewire\AsignacionRutaController;
use App\Livewire\DisenioController;
use App\Livewire\InventarioController;

use App\Livewire\MaterialController;
use App\Livewire\ProductoController;
use App\Livewire\TipoController;
use App\Livewire\ClienteController;
use App\Livewire\CompraController;
use App\Livewire\CreditoController;
use App\Livewire\CuentaCobrarController;
use App\Livewire\EnvioController;
use App\Livewire\EstadoCuentaController;
use App\Livewire\EstadoCuentaVentaController;
use App\Livewire\EstadoEnvioController;
use App\Livewire\NotaCreditoController;
use App\Livewire\ProveedorController;
use App\Livewire\RoleController;
use App\Livewire\RutaController;
use App\Livewire\ServicioController;
use App\Livewire\SucursalController;
use App\Livewire\TrasladoController;
use App\Livewire\UsuarioController;
use App\Livewire\VehiculoController;
use App\Livewire\VentaController;
use App\Livewire\VentaRapidaController;

Route::view('/', 'welcome');


    Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

    Route::middleware('auth')->group(function () {
       /* Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
       */



        Route::get('marca', MarcaController::class)->middleware('can:marca')->name('marca');
        Route::get('tipo', TipoController::class)->middleware('can:tipo')->name('tipo');
        Route::get('material', MaterialController::class)->middleware('can:material')->name('material');
        Route::get('disenio', DisenioController::class)->middleware('can:disenio')->name('disenio');
        Route::get('producto', ProductoController::class)->middleware('can:producto')->name('producto');
        Route::get('inventario', InventarioController::class)->middleware('can:inventario')->name('inventario');
        Route::get('cliente', ClienteController::class)->middleware('can:cliente')->name('cliente');
        Route::get('proveedor', ProveedorController::class)->middleware('can:proveedor')->name('proveedor');
        Route::get('inicio', InicioController::class)->name('inicio');
        Route::get('compra', CompraController::class)->middleware('can:compra')->name('compra');
        Route::get('usuario', UsuarioController::class)->middleware('can:usuario')->name('usuario');
        Route::get('role',RoleController::class)->middleware('can:rol')->name('rol');
        Route::get('venta', VentaController::class)->middleware('can:venta')->name('venta');
        Route::get('estado_cuenta', EstadoCuentaController::class)->name('estado_cuenta');
        Route::get('estado_cuenta_venta', EstadoCuentaVentaController::class)->name('estado_cuenta_venta');
        Route::get('ajuste_inventario', AjusteInventarioController::class)->name('ajuste_inventario');
        Route::get('vehiculo', VehiculoController::class)->name('vehiculo');
        Route::get('envio', EnvioController::class)->name('envio');

        Route::get('credito', CreditoController::class)->name('credito');
        Route::get('cuenta_cobrar', CuentaCobrarController::class)->name('cuenta_cobrar');
        Route::get('venta_rapida', VentaRapidaController::class)->name('venta_rapida');
        Route::get('asignacion_ruta', AsignacionRutaController::class)->name('asignacion_ruta');

        Route::get('ruta', RutaController::class)->name('ruta');

        Route::get('estado_envio', EstadoEnvioController::class)->name('estado_envio');
        Route::get('envio', EnvioController::class)->name('envio');

        Route::get('sucursal', SucursalController::class)->name('sucursal');
        Route::get('traslado', TrasladoController::class)->name('traslado');

        Route::get('servicio', ServicioController::class)->name('servicio');
        Route::get('abono', AbonoController::class)->name('abono');
        Route::get('nota_credito', NotaCreditoController::class)->name('nota_credito');

        //Route::get('generate-pdf', [PDFController::class, 'generatePDF']);



    });

   // Route::get('generate-PDF', [VentaRapidaController::class, 'generatePDF']);

        Route::get('pdf-venta-rapida/{id?}}', [VentaRapidaController::class, 'pdfVentaRapida'])->name('pdfVentaRapida');
        Route::get('pdf-exportar-venta/{id?}}', [VentaController::class, 'pdfExportarVenta'])->name('pdfExportarVenta');

        Route::get('pdf-exportar-abono/{id?}}', [AbonoController::class, 'pdfExportarAbono'])->name('pdfExportarAbono');
        Route::get('pdf-exportar-nota-credito/{id?}}', [NotaCreditoController::class, 'pdfExportarNotaCredito'])->name('pdfExportarNotaCredito');

        Route::get('pdf-exportar-estado-cuenta}', [EstadoCuentaController::class, 'pdfExportarEstadoCuenta'])->name('pdfExportarEstadoCuenta');
        Route::get('pdf-exportar-estado-cuenta/{id?}}', [EstadoCuentaController::class, 'pdfExportarEstadoCuenta'])->name('pdfExportarEstadoCuenta');

        Route::get('pdf-exportar-estado-cuenta-venta}', [EstadoCuentaVentaController::class, 'pdfExportarEstadoCuentaVenta'])->name('pdfExportarEstadoCuentaVenta');
        Route::get('pdf-exportar-estado-cuenta-venta/{id?}}', [EstadoCuentaVentaController::class, 'pdfExportarEstadoCuentaVenta'])->name('pdfExportarEstadoCuentaVenta');


require __DIR__.'/auth.php';
