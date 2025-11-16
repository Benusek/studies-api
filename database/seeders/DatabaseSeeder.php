<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\CommentAnswer;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\Subscribe;
use App\Models\TagVideo;
use App\Models\User;
use App\Models\UserPlaylist;
use App\Models\Video;
use App\Models\VideoCategory;
use Database\Factories\TagVideoFactory;
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

        DB::table('tags')->insert([
            ['name' => 'Гайд'],
            ['name' => 'Теория'],
            ['name' => 'Задача'],
            ['name' => 'Практика'],
            ['name' => 'Для начинающих']
        ]);

        Video::factory(10)->create();
        VideoCategory::factory(10)->create();
        Comment::factory(10)->create();
        CommentAnswer::factory(10)->create();
        TagVideo::factory(10)->create();
        Playlist::factory(10)->create();
        PlaylistVideo::factory(10)->create();
    }
}
