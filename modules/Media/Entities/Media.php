<?php

namespace Modules\Media\Entities;

use Plank\Mediable\Media as PlankMedia;

class Media extends PlankMedia
{

    /**
     *  Get the avatar
     */
    public function photo($type){
        return url('media/'.$this->id.'/media/'.$type.'.jpg');
    }

}