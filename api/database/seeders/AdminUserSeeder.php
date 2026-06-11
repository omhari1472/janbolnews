<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder {
    public function run(): void {
        DB::table('users')->insertOrIgnore([
            'name'       => 'Super Admin',
            'email'      => 'admin@janbolnews.com',
            'password'   => Hash::make('janbolnews2026'),
            'role'       => 'super',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
