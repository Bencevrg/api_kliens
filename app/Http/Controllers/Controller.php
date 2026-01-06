<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected $token;

    function __construct()
    {
        // Minden controller példányosításakor kiolvassuk a tokent a sessionből
        $this->token = session('api_token');
    }

    /**
     * Segédfüggvény annak eldöntésére, hogy be van-e jelentkezve a felhasználó
     */
    protected function isAuthenticated()
    {
        return session()->has('api_token');
    }
}