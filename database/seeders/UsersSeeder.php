<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)
        ->create()
        ->each(function ($user) {
            $profile = UserProfile::factory()->make([
                'user_id' => $user->id,
            ]);
            $user->userProfile()->save($profile);
            $user->assignRole('member');
        });
    }
}
