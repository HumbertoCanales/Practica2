<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Auth
Route::post('/signup','ApiAuth\AuthController@signUp');
Route::post('/login','ApiAuth\AuthController@logIn')->middleware('check.age');
Route::middleware('auth:sanctum')->delete('/logout','ApiAuth\AuthController@logOut');

//Admins
Route::middleware('auth:sanctum')->get('users/abilities','ApiAuth\AuthController@abi');
Route::middleware('auth:sanctum')->post('users/{user}/abilities','ApiAuth\AuthController@grantAbi');
Route::middleware('auth:sanctum')->delete('users/{user}/abilities','ApiAuth\AuthController@revokeAbi');

Route::middleware('auth:sanctum')->get('/posts/comments','CommentController@allPC');
Route::middleware('auth:sanctum')->get('/comments','CommentController@all');
Route::middleware('auth:sanctum')->get('/comments/{comment}','CommentController@show');

Route::middleware('auth:sanctum')->get('/users','UserController@all');
Route::middleware('auth:sanctum')->get('/users/{user}','UserController@show');
Route::middleware('auth:sanctum')->put('/users/{user}','UserController@update');
Route::middleware('auth:sanctum')->delete('/users','UserController@destroyAll');
Route::middleware('auth:sanctum')->delete('/users/{user}','UserController@destroy');

Route::middleware('auth:sanctum')->put('/posts/{post}','PostController@update');
Route::middleware('auth:sanctum')->delete('/posts','CommentController@destroyAll');
Route::middleware('auth:sanctum')->delete('/posts/{post}','PostController@destroy');

Route::middleware('auth:sanctum')->put('/posts/{post}/comments/{comment}','CommentController@update');
Route::middleware('auth:sanctum')->delete('/comments','CommentController@destroyAll');
Route::middleware('auth:sanctum')->delete('/posts/{post}/comments','CommentController@destroyFromPost');
Route::middleware('auth:sanctum')->delete('/posts/{post}/comments/{comment}','CommentController@destroy');

//Logged-in Users
Route::post('/posts','PostController@store');
Route::put('/myposts/{post}','PostController@update');
Route::delete('/myposts/{post}','PostController@destroy');

Route::post('/posts/{post}/comments','CommentController@store');
Route::put('/mycomments/{post}','PostController@update');
Route::delete('/mycomments/{post}','PostController@destroy');

//All Users
Route::get('/posts','PostController@all');
Route::get('/posts/{post}','PostController@show');

Route::get('/posts/{post}/comments','CommentController@allFromPost');
Route::get('/posts/{post}/comments/{comment}','CommentController@show');







