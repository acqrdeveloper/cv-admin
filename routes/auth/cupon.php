<?php 
Route::get('/cupon', 				function(){ return view('app'); });
Route::get('/cupon/search', 				'CV\cuponController@search');
Route::get('/cupon/valid/{codigo}',	'CV\cuponController@valid');
Route::post('/cupon', 				'CV\cuponController@insert');
Route::put('/cupon', 				'CV\cuponController@update');
Route::delete('/cupon/{cupon_id}', 			'CV\cuponController@delete');