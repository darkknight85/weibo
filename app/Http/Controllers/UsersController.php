<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('auth',[
            'except'=>['show','create','store']
        ]);

        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
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

    // public function store(Request $request)
    // {
    //     $this->validate($request, [
    //         'name' => 'required|unique:users|max:50',
    //         'email' => 'required|email|unique:users|max:255',
    //         'password' => 'required|confirmed|min:6'
    //     ]);
    //     return;
    // }

    //store方法
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'=>'required|unique:users|max:50', //存在性验证，唯一性验证，长度验证
            'email'=>'required|email|unique:users|max:255',//存在，唯一，邮件格式，长度验证
            'password'=>'required|confirmed|min:6' //存在，重复性，长度验证
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);

        Auth::login($user);
        session()->flash('success','欢迎，您将在这里开启一段新的旅程');
        return redirect()->route('users.show',[$user]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }

    public function update(User $user, Request $request){
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name']=$request->name;
        if($request->password){
            $data['password']=bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功');

        return redirect()->route('users.show',$user);
    }
}
