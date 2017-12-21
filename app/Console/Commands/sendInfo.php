<?php

namespace CVAdmin\Console\Commands;

use Illuminate\Console\Command;

use CVAdmin\CV\Models\Empresa;

class sendInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:info';

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

        $empresas = Empresa::whereIn('preferencia_estado', ['A','S'])->get();

        foreach($empresas as $empresa){

            $r = $empresa->representantes()->first();

            try {
                \Mail::send('mail.html.info', ['fullname'=>$r->nombre. ' ' . $r->apellido], function($m) use ($r){
                    $m->from('noreply@centrosvirtuales.com', 'Centros Virtuales del Perú');
                    $m->to($r->correo, $r->nombre.' '.$r->apellido)->subject('Comunicado');
                });

                echo "Sent to " . $r->correo . PHP_EOL;
            } catch (\Exception $e) {
                echo $e->getMessage() . " in " . $e->getFile() . "(" . $e->getLine() . ")" . PHP_EOL;
            }

        }
    }
}
