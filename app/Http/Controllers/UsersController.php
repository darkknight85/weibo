<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    //create新建用户方法
    public function create()
    {
        return view('users.create');
    }

    //show查看方法
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }
}
