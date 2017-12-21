<?php
// Empresa
Route::get('/empresas', function(){ return view('app'); });
Route::get('/empresas/search', 'CV\empresaController@search');
Route::put('/empresa/setLogin', 'CV\representanteController@setLogin');


Route::post('/empresa', 'CV\empresaController@create');

Route::get('/empresa/{empresa_id}/testprorrateo', 'CV\empresaController@testProrrateo');
Route::get('/empresa/{empresa_id}/send_credentials', 'CV\empresaController@sendCredentials');

Route::get('/empresas/{empresa_id}/extras', 	'CV\empresaController@getExtras');
Route::post('/empresas/{empresa_id}/extras', 'CV\empresaController@postExtras');


Route::put('/empresa/{empresa_id}/activar', 'CV\empresaController@activarAhoraProrateo');

// Cambia ciclo de facturacion
Route::put('/empresa/{empresa_id}/ciclo', 'CV\empresaController@changeCiclo');
// Cambia comprobante
Route::put('/empresa/{empresa_id}/comprobante', 'CV\empresaController@changeComprobante');
// Programar eliminación
Route::delete('/empresa/{empresa_id}/schedule', 'CV\empresaController@scheduleEliminacion');
 

Route::put('/empresa/{id}/info', 'CV\empresaController@updateEmpresa');
Route::put('/empresa/{id}/estado', 'CV\empresaController@updateState');
Route::put('/empresa/{id}/crmestado', 'CV\empresaController@updateCRMState');
Route::put('/empresa/{id}/entrevista', 'CV\empresaController@updateInterview');
Route::get('/empresa/{id}/llamadas/search', 'CV\empresaController@getCalls');
Route::get( '/empresa/{empresa_id}/deuda/{estado}', 'CV\empresaController@getDeuda');

//Servicio
Route::get(    '/empresa/{id}/garantia',      				'CV\empresaController@getFacturaItemGarantia'   );
Route::get(    '/empresa/servicio/horas/{empresa_id}/{ciclo}',      'CV\empresaController@getEmpresaRecursoPeriodoHoras'   );
Route::get(    '/empresa/servicio/{empresa_id}',      'CV\empresaController@getEmpresaServicio'   );
Route::post(   '/empresa/servicio/{empresa_id}',      'CV\empresaController@postEmpresaServicio'  );
Route::put(    '/empresa/servicio/{empresa_id}/{id}', 'CV\empresaController@putEmpresaServicio'   );
Route::delete( '/empresa/servicio/{empresa_id}/{id}', 'CV\empresaController@deleteEmpresaServicio');

//Route::put(    '/empresa/plan/{empresa_id}/{plan_orig}/{plan_nuevo}', 'CV\empresaController@putEmpresaPlanCambio'   );
Route::put('/empresa/{empresa_id}/contrato', 'CV\empresaController@editContract');
Route::put('/empresa/{empresa_id}/renovarcontrato', 'CV\empresaController@renovarEmpresaContrato');

//Plan
Route::post('/empresa/{empresa_id}/plan', 'CV\empresaController@updateplan');
// Crea una nueva boleta/factura
Route::post('/empresa/{empresa_id}/factura', 'CV\facturaController@factura_create');
// Actualiza informacion de la boleta/factura
Route::put('/empresa/{empresa_id}/factura/{factura_id}', 'CV\facturaController@factura_update');
// Agrega/Edita numero a boleta de venta
Route::put('/empresa/{empresa_id}/factura/{factura_id}/agregar_numero', 'CV\facturaController@invoice_update_number');

// Empleado
Route::post('/empresa/{id}/empleado','CV\empleadoController@create');
Route::put('/empresa/{id}/empleado/{empleado_id}','CV\empleadoController@update');
Route::delete('/empresa/{id}/empleado/{empleado_id}','CV\empleadoController@delete');

// Representante
Route::post('/empresa/{id}/representante','CV\representanteController@create');
Route::put('/empresa/{id}/representante/{repr_id}','CV\representanteController@update');
Route::delete('/empresa/{id}/representante/{repr_id}','CV\representanteController@delete');

Route::get('/basic/{id}', 'CV\empresaController@basic');
Route::get('/central/{id}', 'CV\empresaController@getCentral');

/** Comentado por Kevin
 * La nueva ruta se encuenta en factura.php
Route::post('/empresa/{empresa_id}/nota/{factura_id}', 'CV\empresaController@createnota');
*/

// Historial de Acciones
Route::get('/empresa/servicio/{empresa_id}/historial', 'CV\empresaController@getListTipoHistorial');

// Modulos de empresa
Route::get('/empresa/{id}/{modulo?}', function(){ return view('app'); });

// Nueva empresa
Route::get('/nueva_empresa', function(){ return view('app'); });