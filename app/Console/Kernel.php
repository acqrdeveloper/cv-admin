<?php

namespace CVAdmin\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\generaDeuda::class,
        Commands\convenioMensual::class,
        Commands\anularReservaPendiente::class,
        Commands\eliminarEmpresa::class,
        Commands\notificarFactura::class,
        Commands\migratePbx::class,
        Commands\SuspenderEmpresaPorFaltaPago::class,
        Commands\activarPorContrato::class,
        Commands\sendInfo::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
