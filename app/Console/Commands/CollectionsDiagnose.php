<?php

namespace App\Console\Commands;

use App\Entities\Collection;
use Illuminate\Console\Command;

class CollectionsDiagnose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collections:diagnose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnoze collections';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Collection::where('is_private', false)->get()->map(function($item){

            $product = $item->products()->first();

            if($product && $product->media_id) {

                $item->media_id = $product->media_id;

            }

            $categories = $item->products()->pluck('category_id')->toArray();

            if(count($categories)) {

                $categories = array_count_values($categories);

                krsort($categories);

                end($categories);

                $item->category_id = key($categories);

            }

            $item->products_count = count($categories);

            $item->save();

        });
    }

}