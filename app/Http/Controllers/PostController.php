<?php

namespace App\Http\Controllers;

use App\Post;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class PostController extends Controller
{
    public function all()
    {
        $all = Post::all();
        return response()->json($all, 200);
    }
    
    public function store(Request $request)
    {
        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('post:publish')){
            $request->validate([
                'title' => 'required|min:1|max:50',
                'content' => 'required|min:1|max:255'
            ]);
            $post = Post::create([
                    'user_id' => $request->user()->id,
                    'title' => $request->title,
                    'content' => $request->title
            ]);
            return response()->json($post, 201);
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }
    
    public function show($id)
    {
        $post = Post::find($id);
        if($post){
            return response()->json($post, 200);
        }else{
            return response()->json(['error' => "The post you are looking for doesn't exists.",
                                      'code' => 404], 404);
        }
    }   
  
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if($post){
            if($request->user()->tokenCan('admin:admin') 
            || $request->user()->tokenCan('post:publish') && $post->user_id == $request->user()->id){
                $request->validate([
                    'title' => 'required|min:1|max:50',
                    'content' => 'required|min:1|max:255'
                ]);
                Post::find($id) -> update([
                    'user_id' => $request->user()->id,
                    'title' => $request->title,
                    'content' => $request->title]);
                $post = Post::find($id);
                return response()->json($post, 201);
            }else{
                    return response()->json(['message' => "Unauthorized",
                                             'code' => 401], 401);
            }
        }else{
            return response()->json(['error' => "The post you wanted to update doesn't exists.",
            'code' => 400], 400);
        }
    }

    public function destroyAll(Request $request)
    {
        if($request->user()->tokenCan('admin:admin')){
            Comment::truncate();
            Schema::disableForeignKeyConstraints();
            Post::truncate();
            Schema::enableForeignKeyConstraints();
            return response()->json(['message' => "All the posts and comments have been deleted succesfully.", 'code' => 200], 200);
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::find($id);
        if($post){
            if($request->user()->tokenCan('admin:admin')
            || $request->user()->tokenCan('post:delete') && $post->user_id == $request->user()->id){
                $title = $post['title'];
                Comment::where('post_id', $id)->delete();
                Post::destroy($id);
                return response()->json(['message' => "The post titled '".$title."' has been deleted succesfully.", 'code' => 200], 200);
            }else{
                return response()->json(['message' => "Unauthorized",
                'code' => 401], 401);
            }
        }else{
            return response()->json(['message' => "The post you wanted to delete doesn't exists.",
                                     'code' => 400], 400);
        }
    }
}
