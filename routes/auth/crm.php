<?php
/**
 * User: Gonzalo A. del Portal
 * Date: 18/05/17
 * Time: 18:46
 */

Route::get('seguimiento', function () {
    return view('app');
});
/*
Route::get('seguimiento/list-users', 'CV\crmController@getListUsers');
Route::get('seguimiento/list-planes', 'CV\crmController@getListPlanes');
Route::get('seguimiento/list-crm', 'CV\crmController@getListCrm');
*/
Route::get('seguimiento/list', 'CV\crmController@getList');
Route::get('seguimiento/list-notes-enterprice', 'CV\crmController@getNotesByEnterprice');
Route::post('seguimiento/create-note-crm', 'CV\crmController@postCreateNote');
Route::put('seguimiento/estado', 'CV\crmController@changeCrmEstado');
