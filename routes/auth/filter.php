<?php

Route::get('/representante/filter', 'CV\representanteController@filter');
Route::get('/empleado/filter', 'CV\empleadoController@filter');
Route::get('/pbx/filter', 'Pbx\PbxController@filter');
Route::get('/notificacion', 'CV\notificacionController@get');
Route::put('/notificacion/{id}', 'CV\notificacionController@read');