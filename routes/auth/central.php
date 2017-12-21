<?php 
Route::get('/empresa/{empresa_id}/pbx', 'Pbx\PbxController@getNumbers');
Route::get('/pbx/numbers', 'Pbx\PbxController@getFreeNumbers');
Route::get('/pbx/{customer_id}/{id}/record', 'Pbx\PbxController@getRecord');

Route::post('/pbx', 'Pbx\PbxController@save');
Route::post('/pbx/option', 'Pbx\PbxController@saveOption');

Route::put('/pbx/', 'Pbx\PbxController@active');

Route::delete('/pbx', 'Pbx\PbxController@deletePbx');
Route::delete('/pbx/option', 'Pbx\PbxController@deleteOption');