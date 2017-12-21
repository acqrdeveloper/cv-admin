<?php
namespace CVAdmin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use CVAdmin\CV\Models\Representante;

class FacturaMail extends Mailable {

    use Queueable, SerializesModels;

    public $factura;
    public $vencido;

    public function __construct(\CVAdmin\CV\Models\Factura $f, $v){
    	$this->factura = $f;
    	$this->vencido = $v;
    }

    public function build(){

    	$representante = Representante::where('empresa_id', $this->factura->empresa_id)->where('estado','A')->first();

        if(env('APP_ENV') != 'local'){
            $to_addr = $representante->correo; 
            $to_name = $representante->nombre . ' ' . $representante->apellido;
        } else {
            $to_addr = 'kbaylon@sitatel.com';
            $to_name = 'Kevin';
        }

    	$data = [
    		'fullname' => $representante->nombre . ' ' . $representante->apellido,
    		'vencido' => $this->vencido,
    		'comprobante' => $this->factura->comprobante,
    		'numero' => $this->factura->numero,
    		'vencimiento' => $this->factura->fecha_vencimiento,
    		'monto' => $this->factura->monto,
            'detalle' => \DB::table('factura_item')->where('factura_id', $this->factura->id)->get(['descripcion','precio'])
    	];

    	$this->from('noreply@centrosvirtuales.com', 'Centros Virtuales del Perú E.I.R.L')
    		 ->to($to_addr, $to_name)
    		 ->view('mail.factura', $data);

    }
}