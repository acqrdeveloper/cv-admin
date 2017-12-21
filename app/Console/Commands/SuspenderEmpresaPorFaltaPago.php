<?php
namespace CVAdmin\Console\Commands;
use Illuminate\Console\Command;
class SuspenderEmpresaPorFaltaPago extends Command
{

    protected $signature = 'suspender:empresa';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(){

        $query1 = "
            INSERT INTO empresa_historial( empresa_id, estado, observacion, fecha, empleado )
            SELECT a.empresa_id, 'S', 'Suspendido por Comprobante no Pagado', NOW(), 'SISTEMAS' FROM (
                SELECT empresa_id FROM factura WHERE empresa_id IN (
                    SELECT id FROM empresa WHERE preferencia_estado IN ('A','P')
                ) AND estado = 'PENDIENTE' AND fecha_limite <= DATE(NOW())
            ) a
        ";
        \DB::insert($query1);


        $query2 = "
            UPDATE empresa SET preferencia_estado = 'S' WHERE id IN (
                SELECT a.empresa_id FROM (
                    SELECT empresa_id FROM factura WHERE empresa_id IN (
                        SELECT id FROM empresa WHERE preferencia_estado IN ('A','P')
                    ) AND estado = 'PENDIENTE' AND fecha_limite <= DATE(NOW())
                ) a
            )
        ";
        \DB::update($query2);

    }
}
