<?php

Route::get('/oficina', function(){ return view('app'); });

/**
 * Obtiene los horarios disponibles de una oficina en una fecha determinada
 * Request parameters
 * @param string fecha Fecha de la reserva
 * @param int oficina_id Id de la oficina
 */
Route::get('/oficina/disponibilidad', 'CV\oficinaController@disponibility');
Route::get('/oficina/disponibilidad.v1', 'CV\oficinaController@getSpaceByTime');
Route::get('/oficina/coworking/{loca_id}', 'CV\oficinaController@getCoworking');
Route::put('/oficina/coworking/{oficina_id}/{empresa_id}', 'CV\oficinaController@putCoworking');

/** **/
Route::get('/oficina/anulacion', 'CV\oficinaController@ofiAnulaList');
Route::post('/oficina/anulacion', 'CV\oficinaController@ofiAnulaCreate');
Route::delete('/oficina/anulacion', 'CV\oficinaController@ofiAnulaDelete');

// 
Route::get('/oficina/auditorio/{empresa_id}', 'CV\oficinaController@ofiAuditorioCoffeeBreak');