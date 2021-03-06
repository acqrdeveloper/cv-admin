<?php
//Bandeja
Route::get('/bandeja', function(){ return view('app'); });
Route::get('/bandeja/{message_id}', 										'CV\bandejaController@getMessageDetail');
Route::get('/bandeja/received/{tipo_usuario}/{id}',							'CV\bandejaController@getMyReceivedMessages');
Route::get('/bandeja/send/{tipo_usuario}/{id}', 							'CV\bandejaController@getMySendMessages');
Route::get('/bandeja/{tipo_usuario}/{id}', 									'CV\bandejaController@getMyMessages');
Route::get('/bandeja/searchall', 											'CV\bandejaController@getAllMessages');
Route::post('/bandeja/create', 												'CV\bandejaController@postNewMessages');
Route::put('/bandeja/read/{message_id}', 									'CV\bandejaController@putReadMessages');
Route::put('/bandeja/response/{empresa_id}/{message_id}/{response_id}', 	'CV\bandejaController@putMessageAction');