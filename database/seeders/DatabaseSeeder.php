<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // テストユーザー作成
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Test User2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password2')
        ]);

        // 他のシーダーを呼び出す場合
        // $this->call(OtherSeeder::class);
    }
}
