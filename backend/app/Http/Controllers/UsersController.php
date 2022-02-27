<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    
    public function signup()
    {
        try{
            $result = User::create(['name'=>request('name'),'email'=>request('email'),'password'=>request('password')])->get();
            return $result;
        }
        catch(Exception $e){
            return ["error"=>$e->getMessage(), "message"=>"Account exists!"];
        }
    }

    public function signin(){
        try{
            $result = User::where('email', request('email'))->where('password',request('password'))->get();
            return $result;
        }
        catch(Exception $e){
            return ["error"=>$e->getMessage(), "message"=>"No such user!"];
        }  
    }

  
}
