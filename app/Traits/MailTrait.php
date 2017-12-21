<?php namespace CVAdmin\Traits;

use Mail;

trait MailTrait {

	function sendMail($to, $type, $vars){

		$obj = null;

		switch($type){
			case 'RESERVA_CONFIRMADA':
				$obj = new \CVAdmin\Mail\Reserva('Reserva Confirmada', $vars);
				break;
			case 'NUEVA_CORRESPONDENCIA':
				$obj = new \CVAdmin\Mail\Correspondencia($vars);
				break;
			case 'HORA_AGREGADA':
				$obj = new \CVAdmin\Mail\Hora($vars);
				break;
		}

		if( env('APP_ENV') == 'local' ){
			Mail::to('kbaylon@sitatel.com')->send($obj);
		} else {

			if(is_array($to)){
				$t = $to['email'];
			} else {
				$t = $to;
			}

			Mail::to($t)->send($obj);
		}
	}
}
