<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class BaseFilter
{

    public Request $request;
    protected Builder $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value]));
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }

    public function params($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request->all();
        }
        return $this->request->get($key, $default);
    }

//    public function has($key)
//    {
//        return Arr::has($this->request->all(), $key);
//    }

//    public function user(){
//        if (\request()->user()==null){
//            abort(401,"Unauthorized");
//        }
//        return \request()->user();
//    }
//    public function checkRole(string $role)
//    {
//        return $this->user()->user_role==$role;
//    }
}
