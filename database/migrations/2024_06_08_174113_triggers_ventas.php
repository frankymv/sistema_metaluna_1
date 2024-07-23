<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::unprepared('DROP TRIGGER IF EXISTS `agregar_credito`');
        DB::unprepared('CREATE TRIGGER agregar_credito AFTER INSERT ON ventas
            FOR EACH ROW
            BEGIN
            IF (NEW.forma_pago="CREDI") THEN
                INSERT INTO creditos (no_credito,venta_id,fecha_credito,total_credito,cliente_id,observaciones)
                VALUES (NEW.no_credito, NEW.id,NEW.fecha_venta,NEW.total_credito,NEW.cliente_id,NEW.observaciones_credito);
            END IF;
        END');

    /*
        while total > 0 do
                SET sucursal_origen =(SELECT id FROM sucursals WHERE  prioridad=prioridad_contador);
                IF (SELECT EXISTS (SELECT * FROM producto_sucursal WHERE  producto_id=NEW.producto_id AND sucursal_id=sucursal_origen)) THEN
                    SET cantidad_temp =(SELECT cantidad FROM producto_sucursal WHERE  producto_id=NEW.producto_id AND sucursal_id=sucursal_origen);
                    IF (TOTAL>cantidad_temp) THEN
                        INSERT INTO salidas (venta_id,producto_venta_id,sucursal_id,cantidad) VALUES (NEW.venta_id, NEW.producto_id,sucursal_origen,cantidad_temp);
                        UPDATE producto_sucursal SET cantidad = 0 WHERE producto_id = NEW.producto_id AND sucursal_id = sucursal_id;
                        SET TOTAL=TOTAL-cantidad_temp;
                        ELSE IF (TOTAL<cantidad_temp) THEN
                            INSERT INTO salidas (venta_id,producto_venta_id,sucursal_id,cantidad) VALUES (NEW.venta_id, NEW.producto_id,sucursal_origen,total);
                            UPDATE producto_sucursal SET cantidad = (cantidad_temp-TOTAL) WHERE producto_id = NEW.producto_id AND sucursal_id = sucursal_id;
                            SET TOTAL=0;
                            ELSE IF (TOTAL=cantidad_temp) THEN
                                INSERT INTO salidas (venta_id,producto_venta_id,sucursal_id,cantidad) VALUES (NEW.venta_id, NEW.producto_id,sucursal_origen,cantidad_temp);
                                UPDATE producto_sucursal SET cantidad = (cantidad_temp-TOTAL) WHERE producto_id = NEW.producto_id AND sucursal_id = sucursal_id;
                                SET TOTAL=0;
                            END IF;
                ELSE
                    SET prioridad_contador=prioridad_contador+1;
                END IF;
        end while ;
    */


    /*
        DB::unprepared('DROP TRIGGER IF EXISTS `add_venta_inventario`');
        DB::unprepared('CREATE TRIGGER add_venta_inventario AFTER INSERT ON producto_venta
            FOR EACH ROW
            BEGIN
            DECLARE exist INT;
            DECLARE sucur_id INT;
            DECLARE invent_id INT DEFAULT 0;
            DECLARE invent_cantidad INT DEFAULT 0;

            SET sucur_id = (SELECT sucursal_id FROM venta WHERE id=NEW.venta_id);

            IF (SELECT EXISTS (SELECT * FROM producto_sucursal WHERE producto_id = NEW.producto_id AND sucursal_id = sucur_id)) THEN
                SET invent_cantidad = (SELECT cantidad from producto_sucursal where producto_id = NEW.producto_id and sucursal_id = sucur_id);
                UPDATE producto_sucursal SET cantidad = (invent_cantidad+NEW.cantidad) WHERE producto_id = NEW.producto_id AND sucursal_id = sucur_id;
            ELSE
                INSERT INTO producto_sucursal (producto_id, sucursal_id, cantidad) VALUES (NEW.producto_id, sucur_id,NEW.cantidad);
            END IF;
        END
        ');

        //funcionanod bastante bien
        DB::unprepared('DROP TRIGGER IF EXISTS `add_traslado_inventario`');
        DB::unprepared('CREATE TRIGGER add_traslado_inventario AFTER INSERT ON compra_producto
        FOR EACH ROW
        BEGIN

        DECLARE exist INT;
        DECLARE sucur_id INT;
        DECLARE invent_id INT DEFAULT 0;
        DECLARE invent_cantidad_origen INT DEFAULT 0;

        SET sucur_id = (SELECT sucursal_id FROM compras WHERE id=NEW.compra_id);
        IF (SELECT EXISTS (SELECT * FROM inventarios WHERE producto_id = NEW.producto_id AND sucursal_id = sucur_id)) THEN
            SET invent_id = (SELECT id FROM inventarios WHERE producto_id = NEW.producto_id AND sucursal_id = sucur_id);
            SET invent_cantidad_origen = (SELECT cantidad from inventarios where producto_id = NEW.producto_id and sucursal_id = sucur_id);
            UPDATE inventarios SET cantidad = (invent_cantidad_origen+NEW.cantidad) WHERE id=invent_id;
        ELSE
            INSERT INTO inventarios (producto_id, sucursal_id, cantidad) VALUES (NEW.producto_id, sucur_id,NEW.cantidad);
        END IF;
        END
                ');
        DB::unprepared('DROP TRIGGER IF EXISTS `add_traslado_inventario`');
            DB::unprepared('CREATE TRIGGER add_traslado_inventario AFTER INSERT ON compra_producto
                FOR EACH ROW
                BEGIN
                    DECLARE exist INT;
                    DECLARE sucur INT;
                    DECLARE inven INT;
                    SET sucur = (SELECT sucursal_id FROM compras WHERE id=NEW.compra_id);
                    SET exist = (SELECT existencia FROM productos WHERE id=NEW.producto_id);
                    INSERT INTO inventarios (id,producto_id, sucursal_id, cantidad) VALUES (NEW.id,NEW.producto_id, sucur, NEW.cantidad) ON DUPLICATE KEY UPDATE producto_id = NEW.producto_id, sucursal_id = sucur, cantidad = NEW.cantidad;
                END
            ');
            DB::unprepared('DROP TRIGGER IF EXISTS `add_traslado_inventario`');
            DB::unprepared('CREATE TRIGGER add_traslado_inventario AFTER INSERT ON compra_producto
                FOR EACH ROW
                BEGIN
                DECLARE exist INT;
                    SET exist = (SELECT existencia FROM productos WHERE id=NEW.producto_id);
                    UPDATE productos SET existencia = (exist+NEW.cantidad) WHERE id=NEW.producto_id;
                END
            ');
            DB::unprepared('DROP TRIGGER IF EXISTS `delete_traslado_inventario`');
            DB::unprepared('CREATE TRIGGER delete_traslado_inventario AFTER DELETE ON compra_producto
            FOR EACH ROW
            BEGIN
                DECLARE exist INT;
                    SET exist = (SELECT existencia FROM productos WHERE id=OLD.producto_id);
                    UPDATE productos SET existencia = existencia-OLD.cantidad WHERE id=OLD.producto_id;
                END
            ');
    */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
