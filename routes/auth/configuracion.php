<?php
Route::get('/configuracion', function(){ return view('app'); });

// Plan
Route::get('/plan/search', 'CV\planController@search');
Route::post('/plan', 'CV\planController@create');
Route::put('/plan/{id}', 'CV\planController@update');
Route::put('/plan/{id}/estado', 'CV\planController@updateStatus');

// Local
Route::get('/local/search', 'CV\configController@getLocalList');
Route::post('/local', 'CV\configController@createLocal');
Route::put('/local/{local_id}', 'CV\configController@updateLocal');

// Oficina
Route::get('/oficina/search', 'CV\configController@getOficinaList');
Route::post('/oficina', 'CV\configController@createOficina');
Route::put('/oficina/{oficina_id}', 'CV\configController@updateOficina');
Route::put('/oficina/{oficina_id}/status', 'CV\configController@updateStatusOficina');