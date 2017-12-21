<?php
namespace CVAdmin\Console\Commands;
use Illuminate\Console\Command;
use CVAdmin\Common\Repos\QueryRepo;
class anularReservaPendiente extends Command
{
    protected $signature = 'anular:ReservaPendiente';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(){
        $query = "
        UPDATE reserva 
        SET estado = 'E' 
        WHERE 
            estado = 'P' AND 
            AL_WORKTIME( 
                IF( 
                    ( WEEKDAY( created_at ) > 4 ), 
                    ( DATE( CONCAT( DATE( DATE_ADD( created_at, INTERVAL ((6-WEEKDAY( created_at ))+1) DAY) ), ' 08:00:00' ) ) ), 
                    ( created_at ) 
                ), 
                NOW() 
            ) >= 5";
        $reg = \DB::update( $query );
        echo "Registros actualizados a E (".$reg.") anular:ReservaPendiente";
    }
}
