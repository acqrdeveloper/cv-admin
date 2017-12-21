<?php
/**
 * Created by PhpStorm.
 * User: Del Portal Ch. Gonzalo A.
 * User: QuispeRoque
 * Date: 07/04/17
 * Time: 14:42
 */

Route::get('reporte', function(){ return view('app'); });
Route::get('filter-search-correspondencias','CV\reporteController@filterSearchCorrespondencias');
Route::get('reporte/{tipo}',	'CV\reporteController@reporte');
