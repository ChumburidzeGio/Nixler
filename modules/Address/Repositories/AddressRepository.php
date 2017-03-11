<?php 

namespace Modules\Page\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use App\Repositories\BaseRepository;

class AddressRepository extends BaseRepository
{
    /**
     * Find the page set as homepage
     * @return object
     */
    public function findHomepage()
    {
        return $this->model->where('is_home', 1)->first();
    }

}