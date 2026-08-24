<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email=trim((string)config('awicode.admin.email',''));
        $password=(string)config('awicode.admin.password','');
        if($email===''||$password===''){
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD kosong. Owner tidak dibuat. Isi .env lalu jalankan: php artisan db:seed --class=AdminSeeder');
            return;
        }
        User::updateOrCreate(['email'=>$email],[
            'name'=>config('awicode.admin.name','Awi Code Owner'),
            'password'=>Hash::make($password),
        ]);
        $this->command?->info('Owner/Admin Awi Code siap digunakan.');
    }
}
