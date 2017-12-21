<?php
// GET
Route::get('/factura', function(){ return view('app'); });
Route::get('/factura/search', 'CV\facturaController@search');
Route::get('/factura/send_facturame', 'CV\facturaController@send_facturame');
Route::get('/factura/anula/{empresa_id}/{factura_id}', 'CV\facturaController@factura_anula');
Route::get('/factura/garantia/{empresa_id}', 'CV\facturaController@garantiaList');
Route::get('/sunat/facturasend/{factura_id}', 'CV\facturaController@facturasend');
Route::get('/sunat/notasend/{factura_id}', 'CV\facturaController@notasend');
Route::get('/email/facturasend/{factura_id}', 'CV\facturaController@emailfacturasend');
Route::get('/email/notasend/{factura_id}', 'CV\facturaController@emailnotasend');
Route::get('/nota/search', 'CV\facturaController@nota_search');
Route::get('/factura/getone/{factura_id}', 'CV\facturaController@getone');
Route::get('/factura/report_pagos/{anio}/{mes}', 'CV\facturaController@report_pagos');
Route::get('/factura/factura_item/{factura_id}', 'CV\facturaController@factura_item');
Route::get('/factura/payment_detail/{factura_id}', 'CV\facturaController@payment_detail');
Route::get('/factura/factura_historial/{factura_id}', 'CV\facturaController@factura_historial');
Route::get('/factura/report_facturacion/{anio}/{mes}', 'CV\facturaController@report_facturacion');
Route::get('/factura/facturacion_empresas/{anio}/{mes}/{ciclo}', 'CV\facturaController@facturacion_empresas');

Route::put('/factura/alterarcomprobante/{empresa_id}/{facturaID}/{comprobante}', 'CV\facturaController@changeComprobante');

// POST
Route::post('/factura/{factura_id}/nota', 'CV\facturaController@createNote'); // Crea una nota de credito o debito a la factura
Route::post('/factura/{factura_id}/pagar', 'CV\facturaController@pay'); // Paga una factura
Route::post('/factura/{factura_id}/pagar_garantia', 'CV\facturaController@payWithGuarantee'); // Paga una factura con garantia

Route::get('facturatemporal/{empresa_id}', 'CV\facturaController@getFacturacionTemporal');
Route::post('facturatemporal/{empresa_id}', 'CV\facturaController@setFacturacionTemporal');
Route::put('facturatemporal/{id}/{empresa_id}', 'CV\facturaController@facturadoFacturacionTemporal');
Route::delete('facturatemporal/{id}/{empresa_id}', 'CV\facturaController@deleteFacturacionTemporal');


Route::delete('/pago/{pagoID}', 'CV\facturaController@deletepay'); // borrar un pago

