<?php

namespace App\Services;

use Illuminate\Http\Request;

class Notifications
{


    public static function success(string $string)
    {
        if (!$string) return;
        session()->flash('notifications', [
            'bannerStyle' => 'success',
            'banner' => $string
        ]);
    }

    public static function error(string $string)
    {
        if (!$string) return;
        session()->flash('notifications', [
            'bannerStyle' => 'danger',
            'banner' => $string
        ]);
    }

    public static function notice(string $string)
    {
        if (!$string) return;
        session()->flash('notifications', [
            'bannerStyle' => 'info',
            'banner' => $string
        ]);
    }

    public static function alert(string $string)
    {
        if (!$string) return;
        session()->flash('notifications', [
            'bannerStyle' => 'warning',
            'banner' => $string
        ]);
    }

    public static function getAll()
    {
        return session()->get('notifications', []);
    }

}
