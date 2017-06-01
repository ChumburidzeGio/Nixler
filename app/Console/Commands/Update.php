<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Entities\Article;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update information on server or in database';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $privacy = file_get_contents(resource_path('docs/privacy.en.md'));

        Article::updateOrCreate([
            'slug' => 'privacy',
            'user_id' => 1,
        ], [
            'title:en' => 'Welcome to the Nixler Privacy Policy',
            'body:en' => $privacy
        ]);

    }

}
