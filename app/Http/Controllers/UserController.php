<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function all()
    {
        if($request->user()->tokenCan('admin:admin')){
            $all = User::all();
            return response()->json($all, 200);
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }
    
    public function show($id)
    {
        if($request->user()->tokenCan('admin:admin')){
            $post = Post::find($id);
            if($post){
                return response()->json($post, 200);
            }else{
                return response()->json(['error' => "The post you are looking for doesn't exists.",
                                          'code' => 404], 404);
            }
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }   
  
    public function update(Request $request, $id)
    {
        if($request->user()->tokenCan('admin:admin')){
            if(Post::find($id)){
                $request -> validate([
                    'email' => 'email|required|unique:users',
                    'name' => 'required|min:1|max:30',
                    'age' => 'required|max:999',
                    'password' => 'required|min:6'
                ]);
                    User::find($id) -> update(
                        ['email' => $request->email,
                         'name' => $request->name,
                         'age' => $request->age,
                         'password' => Hash::make($request->password)]);
                    $user = User::find($id);
                    return response()->json($user, 201);
            }else{
                return response()->json(['error' => "The post you wanted to update doesn't exists.",
                                          'code' => 400], 400);
            }
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }

    public function destroyAll()
    {
        if($request->user()->tokenCan('admin:admin')){
            Comment::all()->delete();
            Post::all()->delete();
            User::all()->delete();
            return response()->json(['message' => "All has been deleted succesfully.", 'code' => 200], 200);
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }

    public function destroy($id)
    {
        if($request->user()->tokenCan('admin:admin')){
            $user = User::find($id);
            if($post){
                $name = $user['name'];
                Comment::where('user_id', $id)->delete();
                Post::where('user_id', $id)->delete();
                User::destroy($id);
                return response()->json(['message' => "The user '".$name."' has been deleted succesfully.", 'code' => 200], 200);
            }else{
                return response()->json(['message' => "The post you wanted to delete doesn't exists.",
                                          'code' => 400], 400);
            }
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }
}
