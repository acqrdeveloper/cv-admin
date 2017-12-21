<?php

namespace CVAdmin\Console\Commands;

use Illuminate\Console\Command;

use DB;
use CVAdmin\PBX\Customer;
use CVAdmin\PBX\Extension;
use CVAdmin\PBX\Ivr;
use CVAdmin\PBX\Number;
use CVAdmin\PBX\Option;
use CVAdmin\PBX\Record;

class migratePbx extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $pbx = DB::select("SELECT e.id AS 'empresa_id', e.preferencia_cdr, c.id AS 'numero_id', c.cancion, c.texto, c.numero FROM empresa e JOIN central c ON c.id = e.central_id WHERE e.central = 'S' AND e.preferencia_estado IN ('A','S') AND c.numero != '' AND c.numero != '000-0000'");

        foreach($pbx as $p){

            // create customer
            $customer = new Customer();
            $customer->id = $p->empresa_id;
            $customer->plan_id = 1;
            $customer->server_name = "cv_" . str_random(9);
            $customer->state = 'A';
            $customer->save();

            // create record
            $record = new Record();
            $record->label = 'Ivr';
            $record->song = $p->cancion;
            $record->location = "/var/lib/asterisk/sounds/ivr/ogm/" . $p->preferencia_cdr . "/ivr";
            $record->customer_id = $p->empresa_id;
            $record->save();

            // create ivr
            $ivr = new Ivr();
            $ivr->label = 'Ivr';
            $ivr->record_id = $record->id;
            $ivr->description = $p->texto;
            $ivr->customer_id = $p->empresa_id;
            $ivr->save();

            // create number
            $number = new Number();
            $number->id = $p->numero_id;
            $number->number = '511' . $p->numero;
            $number->customer_id = $p->empresa_id;
            $number->cid_name = $p->preferencia_cdr;
            $number->destiny_type = 'IVR';
            $number->destiny_id = $ivr->id;
            $number->save();


            $opciones = DB::select("SELECT opcion_numero, opcion_nombre, anexo_numero, anexo_nombre, redireccion FROM central_opcion WHERE central_id = ?", [$p->numero_id]);


            foreach($opciones as $opcion){

                $destiny_type = 'OPERATOR';
                $destiny_id = -1;

                // create extension if opcion isn't operator
                if($opcion->opcion_nombre != 'operadora'){

                    $ext = new Extension();
                    $ext->customer_id = $p->empresa_id;
                    $ext->number_id = $p->numero_id;
                    $ext->accountcode = $customer->server_name . '_' . $opcion->anexo_numero;
                    $ext->password = "$".str_random(8)."$";
                    $ext->label = $opcion->anexo_numero;
                    $ext->label_name = $opcion->anexo_nombre;
                    $ext->redirect_to = $opcion->redireccion;
                    $ext->redirect_to_ringtime = 60;
                    $ext->save();

                    $destiny_type = 'EXTENSION';
                    $destiny_id = $ext->id;
                }

                // search
                $opt = DB::connection('pbx')->table('ivr_option')->where('ivr_id', $ivr->id)->where('option', $opcion->opcion_numero)->first();

                // create ivr option
                if(is_null($opt)){
                    DB::connection('pbx')->table('ivr_option')->insert([
                        'ivr_id' => $ivr->id,
                        'option' => $opcion->opcion_numero,
                        'destiny_type' => $destiny_type,
                        'destiny_id' => $destiny_id,
                        'customer_id' => $p->empresa_id
                    ]);   
                }
            }
        }
    }
}
