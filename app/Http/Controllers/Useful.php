<?php
/**
 * Created by PhpStorm.
 * User: QuispeRoque
 * Date: 22/04/17
 * Time: 09:13
 */

namespace CVAdmin\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

// DateTime
define('FECHA_DEFAULT_FORMAT', Carbon::now()->format('Y-m-d'));
define('FECHA', Carbon::now()->format(FECHA_DEFAULT_FORMAT));
define('FECHA_HORA', Carbon::now()->format(FECHA_DEFAULT_FORMAT . ' H:i:s'));
define('FECHA_DETALLE', Carbon::now()->format('Ymd') . '_' . Carbon::now()->format('His'));
define('FECHA_FIRMA', Carbon::now()->format(FECHA_DEFAULT_FORMAT . ' H:i:s'));

trait Useful
{
    //utiles
    public $service;//lógica
    public $request;//parametros
    public $ajax;//peticion_ajax
    public $repo;//peticion_ajax
    public $rpta = [];//variable generica de respuesta
    //estilos PHPExcel
    public $textAlignHCenter = ['alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER]];
    public $textAlignVCenter = ['alignment' => ['horizontal' => \PHPExcel_Style_Alignment::VERTICAL_CENTER]];
    public $textAlignHRight = ['alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT]];
    public $textAlignHLeft = ['alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT]];
    public $borderAllBordersTHIN = ['borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]];
    public $borderOutlineTHIN = ['borders' => ['outline' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]];
    public $colorFillBlueSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '2196F3']]];
    public $colorFillGreenSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '4caf50']]];
    public $colorFillTealSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '009688']]];
    public $colorFillGreySOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '9e9e9e']]];
    public $colorFillBlueGreySOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '607d8b']]];
    public $colorFillYellowSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'ffeb3b']]];
    public $colorFillAmberSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'ffc107']]];
    public $colorFillOrangeSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'ff9800']]];
    public $colorFillIndigoSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '3f51b5']]];
    public $colorFillRedSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'f44336']]];
    public $colorFillNoneSOLID = ['fill' => ['type' => \PHPExcel_Style_Fill::FILL_NONE]];
    public $textBlackBOLD = ['font' => ['bold' => true, 'color' => ['rgb' => '000000']]];
    public $textWhiteBOLD = ['font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']]];
    public $textWhite = ['font' => ['bold' => false, 'color' => ['rgb' => 'ffffff']]];
    public $textBlack = ['font' => ['bold' => false, 'color' => ['rgb' => '000000']]];
    public $textGrey = ['font' => ['bold' => false, 'color' => ['rgb' => '9e9e9e']]];

    /**
     * metodo generico que intercede para el catch y prepara la respuesta generica.
     * @param $exception
     * @param string $title
     * @param string $level
     */
    function returnCatch($exception, $title = 'advertencia', $level = 'warning')
    {
        if ($exception->getCode() > 0) {//si es PDOException

            if (!is_null($exception)) {
                $this->rpta = ['load' => false, 'data' => null, 'error' => $exception->getFile() . ' | ' . $exception->getLine() . ' | ' . $exception->getMessage(), 'detail' => $exception->getPrevious()->errorInfo[2], 'title' => $title, 'level' => $level];
            }

        } else {//si es Exception

            if (!is_null($exception)) {
                $this->rpta = ['load' => false, 'data' => null, 'error' => $exception->getFile() . ' | ' . $exception->getLine() . ' | ' . $exception->getMessage(), 'detail' => '', 'title' => $title, 'level' => $level];
            }
        }
    }

    /**
     * metodo generico que realiza la respuesta generica de satisfaccion
     * @param null $data
     */
    function returnSuccess($data = null)
    {
        $this->rpta = ['load' => true, 'data' => $data];
    }

    function returnSuccessFlash($detail, $data = null, $title = 'bien', $level = 'success')
    {
        $this->rpta = ['load' => true, 'data' => $data, 'detail' => $detail, 'title' => $title, 'level' => $level];
    }

    /**
     * metodo generico para las notificaciones a la vista.
     * @param string $title
     * @param  string|null $message
     * @param  string $level
     * @return \Illuminate\Foundation\Application|mixed
     */
    function flash_message($title = 'bien', $message = null, $level = 'success')
    {
        $arr_message = ['title' => $title, 'message' => $message];
        $notifier = app('flash');

        if (!is_null($message)) {
            return $notifier->message($arr_message, $level);
        }

        return $notifier;
    }

    /**
     * metodo generico que realiza un log segun el tipo sea indicado
     * @param $type
     * @param $detail
     * @param null $data
     */
    function doLog($type, $detail)
    {
        //Establecemos zona horaria por defecto
        date_default_timezone_set('America/Lima');
        $path = storage_path() . '/logs/';

        switch ($type) {
            case 'error':
                Log::useFiles($path . 'error.log');
                Log::error($detail);
                break;
            default:
                Log::useFiles($path . 'info.log');
                Log::error($detail);
                break;
        }
    }

    /**
     * metodo generico que devuelve el maximo ID de una tabla y su AUTOINCREMENT
     * @param $table
     * @param null $field
     * @return int
     */
    function getSumMaxID($table, $field = null)
    {
        $maxID = DB::table($table)->max(($field == null) ? 'id' : $field);
        return (int)$maxID + 1;
    }

    /**
     * metodo generico que realiza la creacion de una hoja EXCEL
     * @param $objPHPExcel
     * @param $headers
     * @param $columns
     * @param $title
     * @param int $row
     * @param bool $merge
     * @return mixed
     */
    protected function fnCreateExcel($objPHPExcel, $headers, $columns, $title, $row = 1, $merge = false)
    {
        $objPHPExcel
            ->getProperties()
            ->setCreator('aquispe.developer@gmail.com')
            ->setTitle($title);

        $objPHPExcel->setActiveSheetIndex(0);
        $worksheet = $objPHPExcel->getActiveSheet();
        $total = count($columns);

        for ($i = 0; $i < $total; $i++) {
            if ($merge) {
                if (is_array($columns[$i])) {
                    $foo = $columns[$i][0] . $row . ':' . $columns[$i][1] . $row;
                    $worksheet->mergeCells($foo);
                    $worksheet->setCellValue($columns[$i][0] . $row, $headers[$i]);
                } else {
                    $worksheet->getColumnDimension($columns[$i])->setAutoSize(true);
                    $worksheet->setCellValue($columns[$i] . $row, $headers[$i]);
                }
            } else {
                $worksheet->getColumnDimension($columns[$i])->setAutoSize(true);
                $worksheet->setCellValue($columns[$i] . $row, $headers[$i]);
            }
        }

        if (is_array($columns[$total - 1])) {
            $oneColumn = $columns[$total - 1][1];
        } else {
            $oneColumn = $columns[$total - 1];
        }

        // Dejar estatico solo la primera fila
        $worksheet->freezePane('A' . ($row + 1));
        $styleArray = array(
            'font' => array(
                'bold' => true
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $worksheet->getStyle($columns[0] . $row . ':' . $oneColumn . $row)->applyFromArray($styleArray);
        return $worksheet;
    }

    /**
     * metodo generico para descargar el archivo Excel
     * @param $objPHPExcel
     * @param $filename
     * @param string $type
     * @internal param null $options
     */
    protected function fnExportExcel($objPHPExcel, $filename, $type = 'Excel5')
    {
        $objPHPExcel->getActiveSheet()->setShowGridlines(false);
        $filename = $filename . '_' . DETALLE;

        if ($type == 'Excel5' || $type == 'Excel2007') {
            if ($type == 'Excel5') {
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
            } else {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            }

            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

        } else if ($type == 'PDF') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment="inline";filename="' . $filename . '.pdf"');
            header('Cache-Control: max-age=0');
            header("Cache-Control: private");

        }

        $excelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $type);
        $excelWriter->save('php://output');
    }

    /**
     * metodo generico que procesa y crea archivo pdf
     * @param $dompdf
     * @param $viewHtml
     * @param null $config
     */
    protected function fnGeneratePDF($dompdf, $viewHtml, $config = null)
    {
        if (is_null($config)) {
            $config = [
                'attachment' => '0',
                'hoja' => 'A4',
                'filename' => 'test.pdf',
                'orientation' => 'p',
            ];
        }
        $dompdf->loadHtml($viewHtml);
        $dompdf->setPaper($config['hoja'], $config['orientation'] == 'P' ? 'portrait' : 'landscape');
        $dompdf->render();
        $dompdf->stream($config['filename'], ['Attachment' => $config['attachment']]);
    }

}

class  ConvertirNumeroALetra
{

    /**
     * Created by PhpStorm.
     * User: arielcr
     * Modified: aquispe
     * Date: 2017/05/22
     * Time: 09:42
     */

    private static $UNIDADES = [
        '',
        'UN',
        'DOS',
        'TRES',
        'CUATRO',
        'CINCO',
        'SEIS',
        'SIETE',
        'OCHO',
        'NUEVE',
        'DIEZ',
        'ONCE',
        'DOCE',
        'TRECE',
        'CATORCE',
        'QUINCE',
        'DIECISEIS',
        'DIECISIETE',
        'DIECIOCHO',
        'DIECINUEVE',
        'VEINTE'
    ];

    private static $DECENAS = [
        'VENTI',
        'TREINTA',
        'CUARENTA',
        'CINCUENTA',
        'SESENTA',
        'SETENTA',
        'OCHENTA',
        'NOVENTA',
        'CIEN'
    ];

    private static $CENTENAS = [
        'CIENTO ',
        'DOSCIENTOS ',
        'TRESCIENTOS ',
        'CUATROCIENTOS ',
        'QUINIENTOS ',
        'SEISCIENTOS ',
        'SETECIENTOS ',
        'OCHOCIENTOS ',
        'NOVECIENTOS '
    ];

    public static function convertir($number, $moneda = '', $centimos = '', $forzarCentimos = false)
    {
        $converted = '';
        $decimales = '';

        if (($number < 0) || ($number > 999999999)) {
            die ('No es posible convertir el numero a letras');
        }

        $div_decimales = explode('.', $number);

        if (count($div_decimales) > 1) {
            $number = $div_decimales[0];
            $decNumberStr = (string)$div_decimales[1];
            if (strlen($decNumberStr) >= 2) {
                $decNumberStrFill = str_pad($decNumberStr, 9, '0', STR_PAD_LEFT);
                $decCientos = substr($decNumberStrFill, 6);
                $decimales = self::convertGroup($decCientos);
            }
        } else if (count($div_decimales) == 1 && $forzarCentimos) {
            $decimales = 'CERO';
        }

        $numberStr = (string)$number;
        $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);

        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'UN MILLON ';
            } else if (intval($millones) > 0) {
                $converted .= sprintf('%s MILLONES ', self::convertGroup($millones));
            }
        }

        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'MIL ';
            } else if (intval($miles) > 0) {
                $converted .= sprintf('%s MIL ', self::convertGroup($miles));
            }
        }

        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'UN ';
            } else if (intval($cientos) > 0) {
                $converted .= sprintf('%s ', self::convertGroup($cientos));
            }
        }

        if (empty($decimales)) {
            $valor_convertido = $converted . strtoupper($moneda);
        } else {
            if (true) {
                $valor_convertido = $converted . strtoupper($moneda) . ' CON ' . $decimales . ' ' . strtoupper($centimos);
            } else {
                $valor_convertido = $converted . strtoupper($moneda) . ' CON ' . $decimales . ' ' . strtoupper($centimos);
            }
        }

        return $valor_convertido;
    }

    private static function convertGroup($n)
    {
        $output = '';

        if ($n == '100') {
            $output = "CIEN ";
        } else if ($n[0] !== '0') {
            $output = self::$CENTENAS[$n[0] - 1];
        }

        $k = intval(substr($n, 1));

        if ($k <= 20) {
            $output .= self::$UNIDADES[$k];
        } else {
            if (($k > 30) && ($n[2] !== '0')) {
                $output .= sprintf('%s Y %s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            } else {
                $output .= sprintf('%s%s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            }
        }

        return $output;
    }
}