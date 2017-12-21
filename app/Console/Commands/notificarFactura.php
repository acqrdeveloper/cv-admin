<?php

namespace CVAdmin\Console\Commands;

use Mail;
use Illuminate\Console\Command;
use CVAdmin\CV\Models\Factura;

class notificarFactura extends Command {

    protected $signature = 'factura:notificar';

    protected $description = 'Notifica a los clientes por correo sobre el estado de sus facturas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

    	$now = date('Y-m-d');

    	$facturas = Factura::where('estado','PENDIENTE')->whereRaw("(fecha_emision = CURDATE() OR DATE_ADD(DATE(fecha_emision), INTERVAL 1 DAY) = CURDATE() OR fecha_vencimiento = CURDATE() OR fecha_limite = CURDATE())")->get();

    	foreach($facturas as $factura){
    		if($factura->fecha_emision == $now){
    			$day = -1;
    		} elseif($factura->fecha_vencimiento == $now) {
    			$day = 0;
    		} elseif($factura->fecha_limite == $now) {
    			$day = 1;
    		}

    		try {
				Mail::send(new \CVAdmin\Mail\FacturaMail($factura, $day));    			
    		} catch (\Exception $e) {
    		}
    	}
    }
}