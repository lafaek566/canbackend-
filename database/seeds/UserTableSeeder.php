<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\UserProfile; 

class UserTableSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat User Admin
        $user = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@caraudio.com',
            'password' => Hash::make('Admin123'),
            'role_id' => 1,
        ]);
        
        // 2. Buat UserProfile untuk User Admin tersebut
        UserProfile::create([
            'user_id' => $user->id,
            'avatar' => null, 
            'banner' => null,
            'phone_no' => '0800123456',
            'biography' => 'This is the main administrator profile.',
        ]);
    }
}