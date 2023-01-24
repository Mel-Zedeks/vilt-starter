<?php

namespace App\Services;

class PageTitle
{

    public static function change(string $string)
    {
        session()->put('page_title',$string);
    }

    public static function getCurrent()
    {
        return session()->has('page_title')?session()->get('page_title'):config('app.name');
    }


}
