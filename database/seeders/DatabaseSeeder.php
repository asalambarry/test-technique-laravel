<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::factory(5)->create();

        User::factory(20)->create()->each(function ($user) {
            $services = Service::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $user->services()->attach($services);
        });
    }
}
