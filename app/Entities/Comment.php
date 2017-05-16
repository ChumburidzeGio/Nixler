<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;
use DB;

class Comment extends Model
{
    use ValidatingTrait;
    
    public $table = 'comments';
    
    protected $fillable  = [
        'user_id',  'target_id',  'target_type', 'text', 'rate'
    ];

    protected $with = ['author'];

    protected $rules = [
        'user_id'   => 'required|numeric',
        'target_id'   => 'required|numeric',
        'target_type'   => 'required|string',
        'text'   => 'required|string',
        'rate'   => 'nullable|numeric|min:1|max:5',
    ];

    protected $throwValidationExceptions = true;


    /**
     * Show comments for model
     */
    public function author()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id', 'id');
    }

    /**
     * Get comment liked attribute
     *
     * @param  int  $value
     * @return bool
     */
    public function getLikedAttribute($value)
    {
        return !!($value);
    }




    /**
     * Scope a query to attach liked attribute to comments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithLikes($query, $actor = 'auth')
    {
        if($actor == 'auth'){
            $actor = auth()->user();
        }

        if($actor){
            return $query->leftJoin('comment_likes as cml', function ($join) use ($actor) {

                $join
                ->where('cml.user_id', '=', $actor->id)
                ->on('comments.id', '=', 'cml.comment_id');

           })->select('comments.*', 'cml.id as liked');
        }

        else {
            return $query;
        }
    }





    /**
     * Scope a order comments chronologically
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortBy($query, $sort)
    {

        if($sort == 'most_recent'){
            return $query->latest('id');
        } 

        elseif ($sort == 'most_helpful'){
            return $query->latest('likes_count');
        }

        elseif ($sort == 'higest_score'){
            return $query->latest('rate', 'desc');
        }

        elseif ($sort == 'lowest_score'){
            return $query->orderBy('rate');
        }

    }





    /**
     * Scope to group comments by rates
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupRates($query)
    {
        return $query->groupBy( 'comments.rate' )
            ->select( 'comments.rate', DB::raw( 'COUNT( comments.id ) as total' ) );

    }





    /**
     * Toggle like comment
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function toggleLike($actor = 'auth')
    {
        if($actor == 'auth'){
            $actor = auth()->user();
        }

        $comment_likes = (new CommentLike)->firstOrCreate([
            'user_id' => $actor->id,
            'comment_id' => $this->id
        ]);

        $this->increment('likes_count');

        if($comment_likes->wasRecentlyCreated){
          return true;
        }

        $comment_likes->delete();

        return false;
    }

}