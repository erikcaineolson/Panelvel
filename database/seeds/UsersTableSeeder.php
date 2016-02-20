<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    protected $users;

    protected function initSeeder()
    {
        $this->users = [
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->initSeeder();

        foreach ($this->users as $user) {
            DB::table('users')->insert([
                'name'     => $user['name'],
                'email'    => $user['email'],
                'password' => $user['password'],
            ]);
        }
    }
}
