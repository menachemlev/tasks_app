<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TasksController extends Controller
{
    public function backup_tasks(){
        try{
            $tasks = request('tasks');
            Task::where('email', request('email'))->where('password',request('password'))->delete();
            foreach($tasks as $task)
            {
                $arr_values = ['title'=> $task['title'],'description'=> $task['desc'],'email'=>  request('email'),'password'=> request('password')];
                Task::create($arr_values);
            }
                }
                catch(Exception $e){
                    return ["error"=>$e->getMessage(), "message"=>"Could not backup tasks!"];
                } 
    }

    public function get_tasks(){
        try{
            $results = Task::where('email', request('email'))->where('password',request('password'))->get();
            return $results;
        }
        catch(Exception $e){
            return ["error"=>$e->getMessage(), "message"=>"Could not get tasks!"];
        }  
    }
}
