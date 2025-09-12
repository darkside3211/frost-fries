<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\text;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with a specified role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = text(
            label: 'Name',
            placeholder: 'E.g. John Doe',
            required: true
        );

        $email = text(
            label: 'Email address',
            placeholder: 'E.g. user@example.com',
            required: true,
            validate: fn (string $value) => match (true) {
                !filter_var($value, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                User::where('email', $value)->exists() => 'A user with this email already exists.',
                default => null
            }
        );

        $password = password(
            label: 'Password',
            required: true,
            validate: fn (string $value) => match (true) {
                strlen($value) < 8 => 'The password must be at least 8 characters.',
                default => null
            }
        );

        $role = select(
            label: 'Role',
            options: [
                'admin' => 'Admin',
                'cashier' => 'Cashier',
            ],
            default: 'cashier'
        );

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);

        $this->info("User {$name} <{$email}> created successfully with the '{$role}' role.");

        return self::SUCCESS;
    }
}
