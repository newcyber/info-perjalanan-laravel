<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Buat akun administrator dengan password yang dimasukkan secara interaktif';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Nama administrator'));
        $email = strtolower(trim((string) $this->ask('Email administrator')));

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = $this->secret('Password administrator (minimal 12 karakter)');
        $confirmation = $this->secret('Ulangi password');

        if (
            ! is_string($password) ||
            strlen($password) < 12 ||
            ! is_string($confirmation) ||
            ! hash_equals($password, $confirmation)
        ) {
            $this->error('Password minimal 12 karakter dan kedua input harus sama.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
        ]);

        $this->info('Akun administrator berhasil dibuat.');

        return self::SUCCESS;
    }
}
