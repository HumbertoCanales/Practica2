<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function all()
    {
        $all = Post::all();
        return response()->json($all, 200);
    }
    
    public function store(Request $request)
    {
        if($request->user()->tokenCan('admin:admin')){
            $validator = Validator::make($request->all(), [
                'author' => 'required|min:1|max:30',
                'title' => 'required|min:1|max:50',
                'content' => 'required|min:1|max:255'
            ]);
            $errors = $validator->errors();
            
            if($validator->fails()){
                return response()->json($errors, 400);
            }else{
                $post = Post::create($request->all());
                return response()->json($post, 201);
            }
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
        if($request->user()->tokenCan('admin:admin')){
            if(Post::find($id)){
                $validator = Validator::make($request->all(), [
                    'author' => 'required|min:1|max:30',
                    'title' => 'required|min:1|max:50',
                    'content' => 'required|min:1|max:255'
                ]);
                $errors = $validator->errors();
                
                if($validator->fails()){
                    return response()->json($errors, 400);
                }else{
                    Post::find($id) -> update(
                        ['title' => $request['title'],
                        'content' => $request['content'],
                        'author' => $request['author']
                        ]);
                    $post = Post::find($id);
                    return response()->json($post, 201);
                }
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
            return response()->json(['message' => "All the posts and comments have been deleted succesfully.", 'code' => 200], 200);
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }

    public function destroy($id)
    {
        if($request->user()->tokenCan('admin:admin')){
            $post = Post::find($id);
            if($post){
                $title = $post['title'];
                Comment::where('post', $id)->delete();
                Post::destroy($id);
                return response()->json(['message' => "The post titled '".$title."' has been deleted succesfully.", 'code' => 200], 200);
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
