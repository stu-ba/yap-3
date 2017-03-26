<?php

use Illuminate\Database\Seeder;

class SystemUserSeeder extends Seeder
{
    public $attributes = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Yap\Models\User::class, 'system')->create();
    }
}
