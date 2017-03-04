<?php

namespace App\Repositories\User;

use Exception, DB;
use Nixler\People\Services\LocationService;

class SettingsRepository extends Repository
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
         * Setup user to work on
         *
         * @return model
         */ 
        public function user() { 

            $user = auth()->user();

            if(auth()->guest()){
                abort(403);
            }

            return $user;
        }


        /**
         * Update user account settings
         *
         * @param array $data
         *
         * @return model
         */ 
        public function updateAccount($data) { 

            $user = $this->user();
            
            $oldUsername = $user->username;

            $user->username = $data['username'];

            $user->name = $data['name'];

            $user->setMeta('headline', $data['headline']);

            if($user->save()){
                $user->updateUsernameCallback($oldUsername, $user->username);
            }

        }



        /**
         * Update user account settings
         *
         * @param array $data
         *
         * @return model
         */ 
        public function updatePassword($new_password) { 

            $user = $this->user();

            $user->password = bcrypt($new_password);

            $user->save();

        }



        /**
         * Create new email address for user
         *
         * @param string $email
         *
         * @return boolean
         */ 
        public function addEmail($email) { 

            $user = $this->user();

            $email = $user->emails()->create([
                'address' => $email
            ]);

            if(!$email->verify()){
                $email->delete();
                return false;
            }

            return true;
        }



        /**
         * Verify email address
         *
         * @param integer $id
         *
         * @return boolean
         */ 
        public function updateEmail($id, $action, $param = null) { 

            $user = $this->user();

            $email = $user->emails()->find($id);

            if($action == 'verify' && $email && !$email->is_verified){
                return $email->verify();
            }

            elseif($action == 'checkCode'){
                return $email->makeVerified($param);
            }

            elseif($action == 'delete' && $user->emails()->count() > 1){
                return $email->delete();
            }

            elseif($action == 'makeDefault'){
                return $email->makeDefault();
            }

            return false;
        }


        /**
         * Update interface language for user
         *
         * @param string $locale
         */ 
        public function updateLocale($locale) { 

            return (new LocationService)->updateLocaleByKey($locale);

        }


        /**
         * Update links to users social profiles
         *
         * @param array $data
         */ 
        public function updateSocialLinks($data) { 
            
            $user = $this->user();

            $user->setMeta('facebook', array_get($data, 'facebook'));
            $user->setMeta('twitter', array_get($data, 'twitter'));
            $user->setMeta('linkedin', array_get($data, 'linkedin'));
            $user->setMeta('vk', array_get($data, 'vk'));
            $user->setMeta('blog', array_get($data, 'blog'));
            $user->setMeta('website', array_get($data, 'website'));

        }

}