<?php

namespace App\Entities;

use Plank\Mediable\Media as PlankMedia;
use App\Traits\NPerGroup;

class Media extends PlankMedia
{
	use NPerGroup;
	
    /**
     *  Get the avatar
     */
    public function photo($type){
        return url('media/'.$this->id.'/media/'.$type.'.jpg');
    }

}