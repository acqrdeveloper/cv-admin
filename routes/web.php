<?php

Route::get('/app/env', function(){
    return config('app.env');    
});

Route::get('/contrato/{empresa_id}', 'CV\empresaController@contratoPDF');

Route::get('/comprobante/pdf/{receptor_ruc}/{documento}/{serie}/{numero}', 'CV\facturaController@comprobantepdf');
Route::get('/comprobante/pdfdownload/{receptor_ruc}/{documento}/{serie}/{numero}', 'CV\facturaController@comprobantepdfdownload');

Route::group(['middleware' => ['auth', 'web']], function () {
    // PRINCIPAL
    Route::get('/', function(){ return view('app'); });
    // CERRAR SESION
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
    // EXPORTACION
    Route::get('/export/{modulo}', 'CV\CommonController@export');
    // MODULOS
	foreach (glob(__DIR__."/auth/*.php") as $filename){
		require_once $filename;
	}
});

Route::group(['middleware' => ['guest']], function () {
	//AUTHENTICACION NORMAL
	Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
	Route::post('login', 'Auth\LoginController@doLogin')->name('post.login');
    Route::get('auth/google', 'Auth\GAuthLoginController@redirectToProvider')->name('google.login');
    Route::get('auth/google/callback', 'Auth\GAuthLoginController@handleProviderCallback');
});

Route::get('/test_mail', function(){
    return view('mail.html.info', ['fullname'=>'Kevin']);
});