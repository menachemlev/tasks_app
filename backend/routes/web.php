<?php

use Illuminate\Support\Facades\Route;


Route::resource('posts', "App\Http\Controllers\PostsController");

/*
Route::get('/insert/{title}/{desc}', function($title, $desc){
    DB::insert('insert into tasks(title,description) values(?, ?)', [$title, $desc]);
});


Route::get('/read', function(){
    $results = DB::select('select * from tasks where id=?', [1]);
    return $results;
});

Route::get('/update', function(){
    $updated = DB::update('update tasks set title = "update title" where id = ?', [1]);
    return $updated;
});

Route::get('/delete', function(){
    $deleted = DB::delete('delete from tasks where id= ?', [1]);

    return $deleted;
});
*/

Route::get('signup/{email}/{name}/{password}',function($email, $name, $password){
    return $email." ".$name." ".$password;
});

?>