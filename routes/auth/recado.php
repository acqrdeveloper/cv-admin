<?php

// Correspondencia
Route::get('/recado', function(){ return view('app'); });
Route::get('/recado/search/{anio}/{mes}/{estado}', 	'CV\recadoController@search');
Route::get('/recado/report/{anio}/{mes}', 			'CV\recadoController@report');

Route::get('/recado/{id}/history', 					'CV\recadoController@getHistory');
Route::post('/recado', 								'CV\recadoController@create');
Route::post('/recado/{id}/deliver', 						'CV\recadoController@deliver');
Route::put('/recado/{id}', 							'CV\recadoController@update');
Route::delete('/recado/{id}', 						'CV\recadoController@delete');