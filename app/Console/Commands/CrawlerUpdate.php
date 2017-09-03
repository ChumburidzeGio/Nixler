<?php

namespace App\Console\Commands;

use App\Repositories\ProductRepository;
use Illuminate\Console\Command;
use App\Entities\ProductSource;
use App\Entities\Product;
use App\Crawler\Crawler;
use App\Services\UrlService;

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

        ProductSource::where('status','success')->chunk(200, function($sources) {

            $sources->filter(function($item) {

                $source = app(UrlService::class)->parse($item->source)->getRootDomain();

                return ('zalando.it' == $source);

            })->map(function($item){

                $this->update($item);

            });

        });

        $secondsUsed = strtotime('now') - $startDate;

        $memoryUsed = $this->formatBytes(memory_get_usage() - $startMemory);

        $this->info("Updated all links for {$secondsUsed} seconds and used {$memoryUsed}");

        info('Crawler update success');

        file_put_contents(public_path('crlw/translations.json'), 
            json_encode(config('crawler.translations'))
        );
    }
    
    /**
     * Update the single product
     *
     * @return void
     */
    private function update($item)
    {
        $this->comment("{$item->product_id}. Updating product from {$item->source}");

        $product = Product::find($item->product_id);

        if(!$product) 
        {
            $this->error("{$item->product_id}. Removed source {$item->id}");

            return $item->delete();
        }

        $crawler = app(Crawler::class);

        $metadata = $item->params ? $crawler->get($item->source, $item->params) : $crawler->get($item->source);
        
        if(!$metadata) 
        {
            return $this->invalidSource($item, $product);
        }

        app(ProductRepository::class)->fillProductFromCrawler($product, $metadata);

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
    
    /**
     * Send invalid source message to console and mark product as inactive
     *
     * @return boolean
     */
    private function invalidSource($item, $product)
    {
        $this->warn("{$item->product_id}. Invalid source {$item->id}");

        $item->update([
            'status' => 'fail'
        ]);

        $product->fresh();

        return $product->markAsInactive();
    }
    
    /**
     * Transform bytes to KB, MB or GB
     *
     * @return string
     */
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('bytes', 'kilobytes', 'megabytes', 'gegabytes');   

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
}