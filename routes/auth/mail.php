<?php 

Route::get('/mail/send_info', function(){

	set_time_limit(60000);

	//\Mail::to('kbaylon@sitatel.com', 'Kevin Baylon')->send(new \CVAdmin\Mail\Information('Kevin Baylon'));
	$emps = \CVAdmin\CV\Models\Empresa::whereIn('preferencia_estado', ['A','S'])->get(['id']);

	$sends = 0;

	foreach($emps as $e){

		try {

			$rep = $e->representantes()->firstOrFail(['nombre','apellido','correo'])->toArray();
			//var_dump($rep);
			\Mail::to($rep['correo'], $rep['nombre'].' '.$rep['apellido'])->send(new \CVAdmin\Mail\Information($rep['nombre'].' '.$rep['apellido']));
			$sends++;

			usleep(500);

		} catch (\Exception $e) {

			echo $e->getMessage() . "<br>";

		}
	}

	echo "Enviados: " . $sends . "<br>"; 
});