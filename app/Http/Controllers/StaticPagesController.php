<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    //define 主页
    public function home()
    {
        return view('static_pages/home');
        //       return '主页';
    }
    //define 帮助页
    public function help()
    {
        return view('static_pages/help');
        // return '帮助页';
    }
    //define 关于页
    public function about()
    {
        return view('static_pages/about');
        // return '关于页';
    }


}
