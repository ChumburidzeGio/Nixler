<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use Modules\Product\Entities\Tag;

class TagRepository extends BaseRepository {


    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Tag::class;
    }


    /**
     * Find or create new tag
     *
     * @return \Illuminate\Http\Response
     */
    public function firstOrCreate($input)
    {
        if(!$input) {
            return false;
        }

        if(!$tag = $this->model->whereTranslation('name', $input)->first()){
            $tag = $this->model->create([
                'name' => $input,
                'user_id' => auth()->id()
            ]);
        }

        return [
            'id' => $tag->id,
            'name' => $tag->name
        ];
    }


    /**
     * Query tags
     *
     * @return \Illuminate\Http\Response
     */
    public function search($query)
    {
        if(!$query) {
            return $this->model;
        }

        return $this->model->whereTranslationLike('name', $query.'%');
    }


}