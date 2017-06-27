<?php

namespace App\Upgrade;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AID
{
    /**
     * Upgrade system to version 1.94
     *
     * Add analitycs using Google Analytics and for this create metrics table and add view count and comments count to products.
     * Improve algorithms for stream and product recommendations.
     * Optimize the code including, moving scheduled tasks to Kernel.
     * Update messages interface and move to one page messanger.
     * Add show/hide to product edit page.
     * Add attachments to comments.
     * Fixed and improved Georgian translations.
     *
     * 1.94a
     * Replace route functions to display photos with media() helper function
     *
     * 1.94b
     * Fix issue with messages and correct typo in app.blade
     * Exclude Nixler account from getting system messages (to not have sending from user 1 to user 1)
     *
     * 1.94c
     * Move product anaylitcs update from ten minutes schedule 1 day
     * Add Georgian version of Privacy Policy
     *
     * 1.95d
     * Add Sessions to settings
     *
     * @return void
     */
    public function upgrade()
    {
        if (!Schema::hasTable('metrics')) {

            Schema::create('metrics', function (Blueprint $table) {

                $table->increments('id');
                $table->string('object_type');
                $table->unsignedInteger('object_id');
                $table->string('key')->index();
                $table->string('value', 255);
                $table->string('target', 255)->nullable();
                $table->date('date');

                $table->index(['object_type', 'object_id', 'date', 'key']);
                
            });

        }

        if (!Schema::hasColumn('products', 'comments_count') && !Schema::hasColumn('products', 'views_count')) {

            Schema::table('products', function (Blueprint $table) {
                $table->integer('comments_count')->default(0)->unsigned();
                $table->integer('views_count')->default(0)->unsigned();
            });

        }

        if (!Schema::hasColumn('comments', 'media_id')) {

            Schema::table('comments', function (Blueprint $table) {
                $table->integer('media_id')->unsigned()->nullable();
            });

        }

    }

    public function messages(){
        return [
        'ka' => "# ნიქსლერი განახლდა (ვერსია 1.94)\n* განახლდა და განვითარდა რეკომენდაციების ალგორითმი, შესაბამისად კიდევ უფრო საინტერესო პროდუქციას მიიღებთ თქვენს სთრიმში და პროდუქტის გვერდზე მსგავსი პროდუქტების სახით.\n* განახლდა მესენჯერის, პარამეტრების და პროდუქტების დიზაინი, ბევრად უფრო ლამაზი და იოლად მოსახმარი გახდა.\n* პროდუქტს დაემატა დამალვა/გამოჩენის ფუნქცია, შესაბამისად თუ რაიმე მიზეზის გამო გსურთ რომ თქვენი პროდუქტი არ გამოჩნდეს საიტზე, მაგრამ არ გსურთ მისი წაშლა, შეგიძლიათ დამალოთ და ნებისმირ დროს გამოაქვეყნოთ \"ჩემი პროდუქტების\" გვერდიდან.\n* კომენტარებს დაემატა სურათის მიმაგრების ფუნქცია, ამიერდან შეგიძლიათ ნიქსლერის სხვა მომხმარებლებს გაუზიაროთ თქვენს მიერი შესყიდული პროდუქტის სურათები.",
        ];
    }

}