<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Image;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \Faker\Factory;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $articles_count = 10;

        $programs_titles = [
            'مشروع اطفال غزة',
            'مشروع اعمار غزة',
            'مشروع علاج اطفال غزة',
            'مشروع تحسين المعيشة في غزة',
        ];


        $faker = Factory::create('ar_SA');
        foreach ($programs_titles as $program_title) {
            $this->command->info("creating program " . $program_title);
            $program = new Program();
            $program->name = $program_title;
            $program->slug = "test" . uniqid();
            $program->content = $faker->realText(100);
            $program->save();
            // $program = \App\Models\Program::create([
            //     "slug" => "test" . uniqid(),
            //     "name" => $programs_titles,
            //     "content" => $faker->realText(100),
            // ]);
            $imageProgram = $program->addMediaFromUrl("https://loremflickr.com/700/500/nature")->toMediaCollection('image');
            $image = new Image();
            $image->id =  $imageProgram->id;
            $image->file_name =  $imageProgram->file_name;
            $image->model()->associate($program);
            $image->save();
        }

        $this->command->info("Sleeping For 5 Seconds!");
        sleep(5);

        for ($i = 0; $i < $articles_count; $i++) {
            $this->command->info("creating article with title " . $faker->realText(50));
            $article = new Article();
            $article->user_id = \App\Models\User::firstOrFail()->id;
            $article->slug = uniqid() . rand(1, 10000);
            $article->title = $faker->realText(50);
            $article->description = $faker->realText(10000);
            $main_image = $article->addMediaFromUrl("https://loremflickr.com/700/500/nature")->toMediaCollection('main_image');
            $article->main_image = $main_image->id . '/' . $main_image->file_name;
            $article->program_id = \App\Models\Program::inRandomOrder()->first()->id;
            $article->save();
        }
    }
}
