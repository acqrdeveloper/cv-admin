<?php
// GET
Route::get('/reserva', function(){ return view('app'); });
Route::get('/reservar', function(){ return view('app'); });
Route::get('/reserva/auditorio/{localID}/{modeloID}/{planID}', 'CV\reservaController@auditorioprecio');
Route::get('/reserva/search/{fecha?}', 'CV\reservaController@search');
Route::get('/reserva/{id}', function(){ return view('app'); });
Route::get('/reserva/{id}/historial', 'CV\reservaController@getHistory');
Route::get('/reserva/{id}/observaciones', 'CV\reservaController@getObservations');
Route::get('/reserva/{id}/detalle', 'CV\reservaController@getDetalle');



// POST
Route::post('/reserva', 'CV\reservaController@create');

// PUT
Route::put('/reserva/{id}', 'CV\reservaController@update');
Route::put('/reserva/{id}/observacion', 'CV\reservaController@updateObs');
Route::put('/reserva/{id}/{estado}','CV\reservaController@changestate');

// DELETE
Route::delete('/reserva/{id}' ,'CV\reservaController@delete');