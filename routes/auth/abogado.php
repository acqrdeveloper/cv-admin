<?php
/**
 * Created by PhpStorm.
 * User: QuispeRoque
 * Date: 02/05/17
 * Time: 13:09
 */

Route::get('abogado', function () {
    return view('app');
});

Route::get('get-all-data-abogado','CV\abogadoController@getAllData');