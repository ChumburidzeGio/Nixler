<?php

namespace App\Repositories\User;

use Exception, DB;

class UserRepository extends Repository
{

        /**
         * Specify Model class name
         *
         * @return mixed
         */
        function model()
        {
            return 'App\User';
        }



        /**
         * Find user by username
         *
         * @param string $username
         *
         * @return array
         *
         * @throws \NotFoundHttpException
         */ 
        public function find($username, $relations = []) { 

            if(auth()->check() && auth()->user()->username == $username){
                $user = auth()->user();
            } else {
                $user = $this->model->where('username', $username)->firstOrFail();
            }

            $user->liked = !!(auth()->check() && auth()->user()->isFollowing($user->id));

            $user->liked_count = $user->liked()->count();
            $user->selling_count = $user->products()->where('status', 'active')->count();
            $user->followers_count = $user->followers()->count();
            $user->followings_count = $user->followings()->count();
            $user->media_count = $user->media()->count();

            $user = $this->setRelations($user, $relations);

            return $user;

        }



        /**
         * Set the relations for user model
         *
         * @param string $relations
         */ 
        public function setRelations($model, $relations = []) { 

            if(in_array('liked', $relations)){
                $model->setRelation('liked', $user->liked()->withMedia()->take(20));
            }

            if(in_array('products', $relations)){
                $products = $user->products()->where('status', 'active')->withMedia()->take(20);
                $model->setRelation('products', $products);
            }

            if(in_array('followers', $relations)){
                $model->setRelation('followers', $user->followers()->take(20));
            }

            if(in_array('followings', $relations)){
                $model->setRelation('followings', $user->followings()->take(20));
            }

            if(in_array('media', $relations)){
                $model->setRelation('media', $user->media()->take(20));
            }

            return $model;

        }


}