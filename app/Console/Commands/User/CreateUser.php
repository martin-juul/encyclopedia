<?php

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        $is_admin = false;
        if ($this->option('admin')) {
            $is_admin = true;
        }

        $data = $this->getUserData();
        if (User::whereEmail($data['email'])->exists()) {
            $this->error('User already exists');
            return 1;
        }

        $user = User::make($data + ['is_admin' => $is_admin]);
        $user->forceFill(['email_verified_at' => now()]);
        $user->saveOrFail();

        $this->info('User created successfully. You may now login.');

        return 0;
    }

    private function getUserData(): array
    {
        return [
            'name'     => $this->ask('name'),
            'email'    => $this->ask('email'),
            'password' => \Hash::make($this->secret('password')),
        ];
    }
}
