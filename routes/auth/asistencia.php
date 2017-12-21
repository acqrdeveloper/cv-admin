<?php
// GET
Route::get('/asistencia', function(){ return view('app'); });
Route::get('/asistencia/search/{tipo}', 			'CV\asistenciaController@search');

// POST
Route::post('/asistencia/{reserva_id}', 			'CV\asistenciaController@massive');
Route::post('/asistencia/{reserva_id}/{nuevo}', 	'CV\asistenciaController@create');

// PUT
Route::put('/asistencia/{reserva_id}/{dni}', 		'CV\asistenciaController@asistencia');

// DELETE
Route::delete('/asistencia/{reserva_id}/{dni}', 	'CV\asistenciaController@delete');