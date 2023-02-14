<?php

namespace App\Services;

use Illuminate\Http\Request;

class Notifications
{


    public static function success(string $string)
    {
        if (!$string) return;
        session()->flash('notifications', [
            'alertStyle' => 'success',
            'alertMessage' => $string
        ]);
    }

    public static function error(string $string)
    {
        if (!$string) return;
        session()->flash('notifications', [
            'alertStyle' => 'error',
            'alertMessage' => $string
        ]);
    }

    public static function notice(string $string)
    {
        if (!$string) return;
        session()->flash('notifications', [
            'alertStyle' => 'info',
            'alertMessage' => $string
        ]);
    }

    public static function alert(string $string)
    {
        if (!$string) return;
        session()->flash('notifications', [
            'alertStyle' => 'warning',
            'alertMessage' => $string
        ]);
    }

    public static function getAll()
    {
        return session()->get('notifications', []);
    }

}
