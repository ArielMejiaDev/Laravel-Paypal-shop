<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name'      =>      'Ariel',
            'email'     =>      'arielsalvadormejia@gmail.com',
            'password'  =>      bcrypt(12345678),
        ]);
    }
}
