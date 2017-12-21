<?php
Route::get('/servicio', function(){ return view('app'); });
Route::get('/servicio/empresa/{empresa_id}', 'CV\servicioController@getEmpresaServicio');
Route::get('/servicio/{empresa_id}/{anio}/{mes}', 'CV\servicioController@getRecursoPeriodo');

Route::post('/servicio/{empresa_id}/{anio}/{mes}', 'CV\servicioController@setRecursoHoras');
Route::post('/servicio/empresa/{empresa_id}', 'CV\servicioController@setEmpresaServicio');
Route::delete('/servicio/empresa/{empresa_id}/{id}', 'CV\servicioController@deleteEmpresaServicio');