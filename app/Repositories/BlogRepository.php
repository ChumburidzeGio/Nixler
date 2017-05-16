<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\Article;
use App\Services\Markdown;

class BlogRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Article::class;
    }
    

    /**
     * @param $slug string
     * @return Article
     */
    public function create($user = null)
    {
        $user = $user ? : auth()->user();

        $model = $this->model->create([
            'user_id' => $user->id,
        ]);

        $model->update([
            'slug' => $model->id
        ]);

        return $model;
    }
    


    /**
     * @param $slug string
     * @return Article
     */
    public function findBySlug($slug)
    {
        $model = $this->model->whereSlug($slug)->firstOrFail();

        $model->body_parsed = (new Markdown)->text($model->body);

        return $model;
    }
    

    /**
     * @param $data array
     * @return Article
     */
    public function update($id, $data, $user = null)
    {
        $user = $user ? : auth()->user();

        $model = $this->findBySlug($id);

        unset($model->body_parsed);

        $model->update([
            'slug' => array_get($data, 'slug'),
            'user_id' => $user->id,
            'title' => array_get($data, 'title'),
            'body' => strip_tags(array_get($data, 'body'))
        ]);

        return $model;
    }

}