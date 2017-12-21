<?php
namespace CVAdmin\Console\Commands;
use Illuminate\Console\Command;
use CVAdmin\Common\Repos\SessionRepo;

class activarPorContrato extends Command
{

    protected $signature = 'activar:contratos';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $query = "
            SELECT a.*, ROUND( a.plan_precio * a.dia / DAY( LAST_DAY( CURDATE() ) ) ) AS 'total' FROM (
                SELECT 
                    a.*,
                    IF( ( a.ciclo = 'MENSUAL'),
                        (   IF( (DAY(a.fecha_inicio)>=15),
                                (   ABS( DATEDIFF( a.fecha_inicio, 
                                        IF( ( DAY(a.fecha_inicio)<15 ),
                                            ( DATE( CONCAT( YEAR(a.fecha_inicio),'-',MONTH(a.fecha_inicio),'-01' ) )  ),
                                            ( DATE( CONCAT( YEAR( DATE_ADD(a.fecha_inicio, INTERVAL 1 MONTH) ),'-',MONTH( DATE_ADD(a.fecha_inicio, INTERVAL 1 MONTH) ),'-01' ) )  )
                                        )
                                        
                                    )  )
                                ),
                                (0)
                            )
                        ),
                        (   IF( (DAY(a.fecha_inicio)>=25 OR DAY(a.fecha_inicio)<=14),
                                (   ABS( DATEDIFF( 
                                        a.fecha_inicio, 
                                        IF( ( DAY(a.fecha_inicio)<15 ),
                                            ( DATE( CONCAT( YEAR(a.fecha_inicio),'-',MONTH(a.fecha_inicio),'-15' ) )  ),
                                            ( DATE( CONCAT( YEAR( DATE_ADD(a.fecha_inicio, INTERVAL 1 MONTH) ),'-',MONTH( DATE_ADD(a.fecha_inicio, INTERVAL 1 MONTH) ),'-15' ) )  )
                                        )
                                        
                                    )  ) 
                                ),
                                (0)
                            )
                        )
                    ) AS 'dia'
                FROM (
                    SELECT a.*, p.precio AS 'plan_precio', e.plan_id, e.preferencia_facturacion AS 'ciclo', p.horas_privada, p.horas_reunion FROM (
                        SELECT id, empresa_id, DATE_ADD(fecha_inicio, INTERVAL 0 DAY) AS 'fecha_inicio', fecha_fin FROM contrato WHERE empresa_id IN ( 
                            SELECT id FROM empresa WHERE preferencia_estado = 'P' 
                        ) AND 
                        estado IN ('VIGENTE','PENDIENTE') AND 
                        fecha_inicio = DATE(NOW())
                    ) a LEFT JOIN empresa e 
                        LEFT JOIN plan p ON p.id = e.plan_id
                    ON e.id = a.empresa_id
                ) a
            ) a ORDER BY a.fecha_inicio
            ";
        $rows  = \DB::select(\DB::raw( $query ));
        foreach( $rows as $r ){
            $r = (array)$r;
            print_r( $r );
            if( $r["dia"] > 0 ){
                $desc  = 'Prorrateo ('.$r["dia"].' Días) por activación adelantado del contrato.';
                ( new SessionRepo )->CallRaw( 
                    "mysql", 
                    "AL_FACTURACION_TEMPORAL_INSERT", 
                    array( 
                        $r["empresa_id"], $r["total"], $desc, $r["fecha_inicio"], 'N', 0,'N' 
                    ) 
                );
                $queyinsert = "INSERT INTO contrato_historial(contrato_id, fecha_inicio, fecha_fin, observacion, created_at)
                VALUES( ".$r["id"].", CURDATE(), ".$r["fecha_fin"].", CONCAT('Se activó ',".$r["dia"]." ,' días antes, de acuerdo al contrato del cliente.'), NOW())";
                \DB::insert($queyinsert);
            }           
            $queryupdate = "UPDATE empresa SET preferencia_estado = 'A' WHERE id = '".$r["empresa_id"]."'";
            \DB::update($queryupdate);

        }
    }
}
