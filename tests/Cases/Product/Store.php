<?php

namespace Tests\Cases\Product;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Entities\Product;

class Store extends TestCase
{
    use WithoutMiddleware;

    private $product;

    private $editUrl;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->product = Product::latest()->first();

        $this->editUrl = route('product.edit', [
            'id' => $this->product->id
        ]);

        $this->updateUrl = route('product:update', [
            'id' => $this->product->id
        ]);

        $this->actingAs($this->product->owner);
    }

    /**
     * @return void
     */
    public function testValidator()
    {
        $response = $this->json('POST', $this->updateUrl, []);

        $response->assertStatus(422); 
    }

    /**
     * @return void
     */
    public function testWithAllData()
    {
        $response = $this->json('POST', $this->updateUrl, [
            'title' => 'Some title',
            'description' => null,
            'variants' => json_encode([[
                'original_price' => null,
                'price' => 14,
                'in_stock' => 12,
                'name' => 'Black'
            ]]),
            'action' => 'publish',
            'media' => json_encode([]),
            'tags' => $this->getTags(),
            'category' => "2",
            'in_stock' => 44,
            'buy_link' => null,
            'sku' => null,
        ]);

        $response->assertRedirect($this->editUrl);
    }

    /**
     * @return void
     */
    public function testWithoutVariants()
    {
        $response = $this->json('POST', $this->updateUrl, [
            'title' => 'Some title',
            'description' => null,
            'variants' => json_encode([]),
            'action' => 'publish',
            'media' => json_encode([]),
            'tags' => json_encode([]),
            'category' => "2",
            'in_stock' => 44,
            'buy_link' => null,
            'sku' => null,
            'price' => 13
        ]);

        $response->assertRedirect($this->editUrl);
    }

    /**
     * Get tags
     *
     * @return json
     */
    public function getTags()
    {
        return json_encode([
            'Black' => 'color',
            'Dress' => 'category'
        ]);
    }
}
