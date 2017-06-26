<?php 

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Repositories\ProductRepository;
use App\Entities\Collection;
use App\Entities\Product;

class CollectionRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Collection::class;
    }


    /**
     * Get all addresses for user
     *
     * @return array
     */
    public function find($id)
    {
    	$collection = $this->model;

        $collection->name = 'Spring 2017 Ready-to-Wear';

        $collection->headline = 'Inside the Mermaid Wonderland of Weeki Wachee Springs';

        $products = app(ProductRepository::class)->transformProducts(
            Product::active()->paginate(20)
        );

        $collection->setRelation('products', $products);

        $collection->user = auth()->user();

        return $collection;
    }

}