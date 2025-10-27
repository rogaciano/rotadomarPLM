<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-admin {email? : O email do usuário que será definido como administrador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Define um usuário como administrador. Se nenhum email for fornecido, o primeiro usuário será definido como administrador.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            // Se um email foi fornecido, encontre o usuário pelo email
            $user = \App\Models\User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("Usuário com o email {$email} não encontrado.");
                return 1;
            }
        } else {
            // Se nenhum email foi fornecido, pegue o primeiro usuário
            $user = \App\Models\User::first();
            
            if (!$user) {
                $this->error('Nenhum usuário encontrado no sistema.');
                return 1;
            }
        }

        // Define o usuário como administrador
        $user->is_admin = true;
        $user->save();

        $this->info("Usuário {$user->name} ({$user->email}) foi definido como administrador com sucesso!");
        return 0;
    }
}
