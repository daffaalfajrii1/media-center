<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateApiToken extends Command
{
    protected $signature = 'api:token
                            {email? : Email user admin pemilik token}
                            {--name=wordpress : Nama token (contoh: wordpress-portal)}';

    protected $description = 'Buat Sanctum API token untuk WordPress / klien eksternal';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email user admin');

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->error("User dengan email [{$email}] tidak ditemukan.");

            return self::FAILURE;
        }

        $name = (string) $this->option('name');
        $token = $user->createToken($name);

        $this->info('Token berhasil dibuat.');
        $this->line('User : '.$user->name.' <'.$user->email.'>');
        $this->line('Nama : '.$name);
        $this->newLine();
        $this->warn('Simpan token ini sekarang. Tidak bisa dilihat lagi nanti:');
        $this->line($token->plainTextToken);

        return self::SUCCESS;
    }
}
