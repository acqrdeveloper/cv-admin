<?php

// Correspondencia y recados
Route::get('/recado-correspondencia/{tab?}', function(){ return view('app'); });

// Rutas de correspondencia
Route::get('/correspondencia/search/{anio}/{mes}/{estado}', 'CV\correspondenciaController@search');
Route::get('/correspondencia/report/{anio}/{mes}', 'CV\correspondenciaController@report');

Route::get('/correspondencia/{id}/history', 'CV\correspondenciaController@getHistory');
Route::post('/correspondencia', 'CV\correspondenciaController@create');
Route::put('/correspondencia/deliver', 'CV\correspondenciaController@deliver');
Route::put('/correspondencia/{id}', 'CV\correspondenciaController@update');
Route::delete('/correspondencia/{id}', 'CV\correspondenciaController@delete');


// Rutas de recado