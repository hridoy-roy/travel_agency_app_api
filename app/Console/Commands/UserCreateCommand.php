<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserCreateCommand extends Command
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
    protected $description = 'Create a new User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('Name Of the new User');
        $user['email'] = $this->ask('Email Of the new User');
        $user['password'] = Hash::make($this->secret('Password Of the new User'));

        $roleName = $this->choice('Role Of this new User', ['admin', 'editor'], 1);
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error('Role Not Found');
            return -1;
        }

        DB::transaction(function () use ($user, $role) {
            $newUser = User::create($user);
            $newUser->roles()->attach($role->id);
        });


        $this->info('User ' . $user['email'] . ' Created Successfully');
        return 0;
    }
}
