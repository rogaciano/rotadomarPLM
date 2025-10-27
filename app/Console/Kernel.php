<?php

namespace App\Console;

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
        Commands\AtualizarEstoqueTecidos::class,
        Commands\SetAdminUser::class,
        Commands\FixLogPermissions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Atualiza o estoque de tecidos a cada 5 minutos
        $schedule->command('tecidos:atualizar-estoque --chunk=5 --memory-limit=32')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/estoque-tecidos.log'));
                 
        // Corrige permissões dos arquivos de log diariamente à meia-noite
        $schedule->command('logs:fix-permissions')
                 ->daily()
                 ->at('00:01')
                 ->appendOutputTo(storage_path('logs/fix-permissions.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
