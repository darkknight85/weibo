<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    //create新建用户方法
    public function create()
    {
        return view('users.create');
    }
}
