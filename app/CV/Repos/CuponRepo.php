<?php
namespace CVAdmin\CV\Repos;
use CVAdmin\CV\Models\Cupon;
use CVAdmin\Common\Repos\SessionRepo;
use CVAdmin\Common\Repos\QueryRepo;
class CuponRepo{
    public function search( $p ){
        return ( new QueryRepo )->Q_cupon( $p );
    }
    public function valid( $codigo ){
        $cupon = Cupon::where('codigo', $codigo)->where( "finicio", "<=", date("Y-m-d") )->where( "ffin", ">=", date("Y-m-d") )->where("usado", 0)->first();

        if(is_null($cupon)){
            throw new \Exception("El cupón ingresado no existe o ya expiró");
        }

        return $cupon;
    }
    private function generateCodigo(){
        $alpha   = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X ', 'Y', 'Z');// 0 - 25
        $anio    = date("Y");
        $anio    = substr( $anio, 0, 1 ) + substr( $anio, 1, 1 ) + substr( $anio, 2, 1 ) + substr( $anio, 3, 1 );
        $anio    = str_pad( $anio, 2, "0", STR_PAD_LEFT );
        $anio    = rand( 1, 2 ) > 1 ? $anio : str_pad( ( $anio <= 25 ? $alpha[$anio] : $alpha[rand(0,25)] ), 2, "0", STR_PAD_LEFT );
        $mes     = $alpha[( date("m") * rand( 1, 2 ) )];
        $dia     = date("d");
        $dia     = $alpha[ ( ( ( substr( $dia, 0, 1 ) ) * rand( 1, 13 ) ) - 1 ) ] . substr( $dia, 1, 1 );
        $hora    = $alpha[date("H")*1];
        $minuto  = date("i");
        $segundo = date("s");
        $segundo = substr( $segundo, 0, 1 ) . $alpha[substr( $segundo, 1, 1 )];
        //$segundo = (date("s")*1);
        //$segundo = str_pad( round( $segundo>25 ? ( $segundo>50 ? $segundo/3 : $segundo/2 ) : $segundo ), 2, "0", STR_PAD_LEFT );
        $codigo  = $anio.$mes.$dia.$hora.$minuto.$segundo;
        return $codigo;
    }
    public function insert( $p ){
        $return = [];
        $codigo = "";
        $inten  = 0;
        $limit  = 5;
        while( $codigo == "" || $inten < $limit ){
            //$codigo = $this->generateCodigo();
            $codigo = strtoupper(str_random(12));
            $val = Cupon::where('codigo', $codigo)->first();
            if( !empty( $val ) ){
                $codigo = "";
            }
            $inten = $inten + 1;
        }
        if( $codigo != "" ){
            $return = Cupon::create(
                array(
                    'created_at'    => date("Y-m-d H:i:s"),
                    'updated_at'    => date("Y-m-d H:i:s"),
                    'reserva_id'    => 0,
                    'codigo'        => $codigo,
                    'finicio'       => $p['finicio'],
                    'ffin'          => $p['ffin'],
                    'usado'         => 0,
                    'monto'         => $p['monto'],
                    'usuario'       => \Auth::user()->nombre
                )
            );
        }
        return $return;
    }
    public function update( $p ){
    	$cupon  = Cupon::where( "id", $p["id"] );
        $cu     = $cupon->first();
        $return = 0;
        if( !empty( $cu ) ){
            $return = Cupon::where( "id", $p["id"] )->update(
                array(
                    'updated_at'    => date("Y-m-d H:i:s"),
                    'reserva_id'    => isset( $p['reserva_id'] ) ? $p['reserva_id'] : $cu['reserva_id'],
                    'finicio'       => isset( $p['finicio']    ) ? $p['finicio']    : $cu['finicio'],
                    'ffin'          => isset( $p['ffin']       ) ? $p['ffin']       : $cu['ffin'],
                    'usado'         => isset( $p['usado']      ) ? $p['usado']      : $cu['usado'],
                    'monto'         => isset( $p['monto']      ) ? $p['monto']      : $cu['monto'],
                    'usuario'       => \Auth::user()->nombre
                )
            );            
        }
        return $return;
    }
    public function delete( $id ){
        return Cupon::where( "id", $id )->where("usado", 0)->delete();
    }
}
?>