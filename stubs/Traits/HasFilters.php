<?php

namespace App\Traits;

use App\Filters\BaseFilter;

trait HasFilters
{

    public function scopeFilter($query, BaseFilter $filter)
    {
        return $filter->apply($query);
    }

}
