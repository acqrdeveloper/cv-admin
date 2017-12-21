<?php

Route::get('/usuario', function(){ return view('app'); });
Route::get('/usuario/all','CV\usuarioController@listAll');
Route::post('/usuario','CV\usuarioController@store');
Route::put('/usuario/{id}','CV\usuarioController@update');
Route::put('/usuario/{id}/estado','CV\usuarioController@changeEstado');

//Route::get('/permisos','CV\usuarioController@getListPermisos');
//Route::get('/modulos','CV\usuarioController@getListModulos');


//Route::put('/usuario/update/roles/{id}','CV\usuarioController@updateRoles');

//
//Route::get('/usuario/roles','CV\usuarioController@getListRoles');
