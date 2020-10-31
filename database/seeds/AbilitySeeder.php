<?php

use Illuminate\Database\Seeder;
use App\Ability;

class AbilitySeeder extends Seeder
{
    public function run()
    {
        Ability::create(['name' => 'admin:admin']);
        Ability::create(['name' => 'user:list']);
        Ability::create(['name' => 'user:profile']);
        Ability::create(['name' => 'post:publish']);
        Ability::create(['name' => 'post:edit']);
        Ability::create(['name' => 'post:delete']);
        Ability::create(['name' => 'com:publish']);
        Ability::create(['name' => 'com:edit']);
        Ability::create(['name' => 'com:delete']);
    }
}
