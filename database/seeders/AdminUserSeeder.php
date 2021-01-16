<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::factory()->create([
                'email' => 'test@123.com'
            ])
            ->each(function ($user) {
                $profile = UserProfile::factory()->make([
                    'user_id' => $user->id,
                    'firstname' => 'Hosni',
                    'middlename' => 'Macrohon',
                    'surname' => 'Bona',
                    'wiresign' => 'HK',
                    'licenseNumber' => '123123',
                    'membership_status' => 'Approved'
                ]);
                $user->userProfile()->save($profile);
                $user->assignRole('admin');
            });
    }
}
