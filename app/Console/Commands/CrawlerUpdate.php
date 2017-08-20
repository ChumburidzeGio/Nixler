<?php

namespace App\Console\Commands;

use App\Repositories\ProductRepository;
use Illuminate\Console\Command;
use App\Entities\ProductSource;
use App\Entities\Product;
use App\Crawler\Crawler;

class CrawlerUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update crawled products';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startDate = strtotime('now');

        $startMemory = memory_get_usage();

        Product::withoutSyncingToSearch(function () {

            ProductSource::where('status','success')->chunk(200, function($sources) {

                $sources->filter(function($item) {

                    $source = app(Crawler::class)->getRootDomain($item->source);

                    return ('zalando.it' == $source);

                })->map(function($item){

                    $this->update($item);

                });

            });

        });

        $secondsUsed = strtotime('now') - $startDate;

        $memoryUsed = $this->formatBytes(memory_get_usage() - $startMemory);

        $this->info("Updated all links for {$secondsUsed} seconds and used {$memoryUsed}");

        file_put_contents(public_path('crlw/translations.json'), 
            json_encode(config('crawler.translations'))
        );
    }

    private function update($item)
    {
       $this->comment("{$item->product_id}. Updating product from {$item->source}");

        $product = Product::find($item->product_id);

        if(!$product) 
        {
            $this->error("{$item->product_id}. Removed source {$item->id}");

            return $item->delete();
        }

        $metadata = app(Crawler::class)->get($item->source);

        if($metadata->isInvalid()) 
        {
            return $this->invalidSource($item, $product);
        }

        $repository = app(ProductRepository::class);

        $repository->syncVariants($metadata->getVariants(), $product);

        $repository->syncTags($metadata->getTags(), $product);

        $product->fill([
            'title' => $metadata->getTitle(),
            'description' => $metadata->getDescription(),
            'category_id' => $metadata->getCategory(),
            'target' => $metadata->getTarget(),
            'sku' => $metadata->getSKU(),
        ]);

        if(!$product->has_variants){

            $product->price = $metadata->getPrice();

            $product->original_price = $metadata->getOriginalPrice();

        }

        $item->update([
            'status' => 'success'
        ]);

        try 
        {
            $product->save();
        } 
        catch (\Exception $e) 
        {
            return $this->invalidSource($item, $product);
        }

        $product->markAsActive();
                    
        $this->info("{$item->product_id}. Updated product from {$item->source}");
    }

    private function invalidSource($item, $product)
    {
        $this->warn("{$item->product_id}. Invalid source {$item->id}");

        $item->update([
            'status' => 'fail'
        ]);

        $product->fresh();

        return $product->markAsInactive();
    }

    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('bytes', 'kilobytes', 'megabytes', 'gegabytes');   

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
}


/*
        $linksUpdated = 0;

        $merchants = ProductSource::groupBy('merchant_id')->pluck('merchant_id')->map(function($merchantId) use ($commander, &$linksUpdated) {

            auth()->loginUsingId($merchantId);

            $query = ProductSource::where('merchant_id', $merchantId);

            $page = 1;

            $count = 100;

            do {

                $chunkStartDate = strtotime('now');

                $results = $query->forPage($page, $count)->pluck('source');

                $links = $results->filter(function($link) {

                    return ('zalando.it' == app(Crawler::class)->getRootDomain($link));

                });

                $countResults = $links->count();

                if ($countResults == 0) {
                    break;
                }

                $commander->info("Updating {$countResults} links from chunk {$page}");

                Product::withoutSyncingToSearch(function () use ($links, $commander) {
                    app(Crawler::class)->bulk($links->toArray(), $commander);
                });

                $secondsUsed = strtotime('now') - $chunkStartDate;

                $commander->comment("Updated {$countResults} links from chunk {$page} for {$secondsUsed} seconds");

                $page++;

                $linksUpdated += $countResults;
                
                file_put_contents(public_path('crlw/zalando.json'), 
                    json_encode(config('crawler.translations.zalando'))
                );

            } while ($countResults == $count);

        });

        info('Crawler updated successfully.');
*/
