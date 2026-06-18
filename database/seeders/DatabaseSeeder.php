<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentAnswer;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\TagVideo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'Пользователь',
                'code' => 'user'
            ]
        ]);
        User::factory(10)->create();
        DB::table('categories')->insert([
            ['name' => 'Мультфильмы'],
            ['name' => 'Анонсы'],
            ['name' => 'Игры'],
            ['name' => 'Обзоры'],
            ['name' => 'Кино'],
            ['name' => 'Книги'],
            ['name' => 'Спорт'],
            ['name' => 'Курсы'],
            ['name' => 'Прочее']
        ]);

        DB::table('tags')->insert([
            ['name' => 'Для детей'],
            ['name' => 'Открытый мир'],
            ['name' => 'Приключения'],
            ['name' => 'Технологии'],
            ['name' => 'Для начинающих']
        ]);

        Playlist::factory(10)->create();
    }
}
