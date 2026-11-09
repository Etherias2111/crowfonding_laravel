<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeCrowfondingAdmin extends Command
{
    protected $signature = 'crowfonding:make-admin {email : Correo de una cuenta ya registrada}';
    protected $description = 'Asigna el rol de administrador a una cuenta existente.';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (! $user) {
            $this->components->error('No se encontró una cuenta con ese correo. Regístrala primero.');
            return self::FAILURE;
        }
        $user->update(['role' => 'admin']);
        $this->components->info("{$user->email} ahora tiene rol de administrador.");
        return self::SUCCESS;
    }
}
