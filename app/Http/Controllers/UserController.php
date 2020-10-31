<?php

namespace App\Http\Controllers;

use App\User;
use App\Post;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function all(Request $request)
    {
        if($request->user()->tokenCan('admin:admin')||$request->user()->tokenCan('user:info')){
            $all = User::all();
            return response()->json($all, 200);
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }
    
    public function show(Request $request, $id)
    {
        if($request->user()->tokenCan('admin:admin')||$request->user()->tokenCan('user:info')){
            $user = User::find($id);
            if($user){
                return response()->json($user, 200);
            }else{
                return response()->json(['error' => "The post you are looking for doesn't exists.",
                                          'code' => 404], 404);
            }
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }

    public function showMP(Request $request, $id)
    {
        if($request->user()->tokenCan('admin:admin')|| $request->user()->tokenCan('profile:info')){
            $user = User::find($request->user()->id);
            return response()->json($user, 200);
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }     
    
    public function updateMP(Request $request, $id)
    {
            if($request->user()->tokenCan('admin:admin')|| $request->user()->tokenCan('user:profile')){
                $request -> validate([
                    'email' => 'email|required',
                    'name' => 'required|min:1|max:30',
                    'age' => 'required|max:999',
                    'password' => 'required|min:6'
                ]);
                if($request->email != $user->email){
                    $user = User::where('email', $request->email)->first();
                    if($user){
                        return response()->json(['email' => "The email hass already been taken.",
                        'code' => 422], 422);
                    }
                }
                    User::find($request->user()->id) -> update(
                        ['email' => $request->email,
                         'name' => $request->name,  
                         'age' => $request->age,
                         'password' => Hash::make($request->password)]);
                    $user = User::find($request->user()->id);
                    return response()->json($user, 201);
            }else{
                return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
            }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if($user){
            if($request->user()->tokenCan('admin:admin')){
                $request -> validate([
                    'email' => 'email|required',
                    'name' => 'required|min:1|max:30',
                    'age' => 'required|max:999',
                    'password' => 'required|min:6'
                ]);
                if($request->email != $user->email){
                    $user = User::where('email', $request->email)->first();
                    if($user){
                        return response()->json(['email' => "The email hass already been taken.",
                        'code' => 422], 422);
                    }
                }
                    User::find($id) -> update(
                        ['email' => $request->email,
                         'name' => $request->name,
                         'age' => $request->age,
                         'password' => Hash::make($request->password)]);
                    $user = User::find($id);
                    return response()->json($user, 201);
            }else{
                return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
            }
        }else{
            return response()->json(['error' => "The user you wanted to update doesn't exists.",
                                     'code' => 400], 400);
        }
    }

    public function destroy(Request $request, $id)
    {
        if($request->user()->tokenCan('admin:admin')){
            $user = User::find($id);
            if($user){
                $name = $user['name'];
                Comment::where('user_id', $id)->delete();
                Post::where('user_id', $id)->delete();
                $user->tokens()->delete();
                User::destroy($id);
                return response()->json(['message' => "The user '".$name."' has been deleted succesfully.", 'code' => 200], 200);
            }else{
                return response()->json(['message' => "The user you wanted to delete doesn't exists.",
                                          'code' => 400], 400);
            }
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }
}
