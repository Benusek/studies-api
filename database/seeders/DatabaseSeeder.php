<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(3)->create();
        // \App\Models\User::factory(10)->create();

        DB::table('roles')->insert([
            [
                'name' => 'Пользователь',
                'code' => 'user'
            ],
            [
                'name' => 'Модератор',
                'code' => 'moderator'
            ]
        ]);

        User::factory(10)->create();
        DB::table('categories')->insert([
            ['name' => 'C#'],
            ['name' => 'Vue'],
            ['name' => 'Next.js'],
            ['name' => 'Node.js'],
            ['name' => 'React'],
            ['name' => 'Flutter'],
            ['name' => 'Go'],
            ['name' => 'Unity'],
            ['name' => 'C++'],
            ['name' => 'Java'],
            ['name' => 'Прочее']
        ]);

    }
}
