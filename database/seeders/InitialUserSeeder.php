<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class InitialUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereEmail('datascribe@scio.systems')->first();
        if (!$user) {
            User::create([
                'firstname' => 'Scio',
                'lastname' => 'Systems',
                'email' => 'datascribe@scio.systems',
                'password' => bcrypt('scio'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
