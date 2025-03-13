<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

class AssignRoles extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = text(
            label: 'User email address to assign roles to',
        );

        $user = User::where(['email' => $email])->first();

        $roles = multiselect(
            label: 'What Roles would you like to assign?',
            options: array_map(function ($value) {
                return $value['name'];
            }, Role::all()->toArray())
        );

        $user->syncRoles($roles);
    }
}
