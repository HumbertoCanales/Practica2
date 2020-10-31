<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Auth
Route::post('/signup','ApiAuth\AuthController@signUp');
Route::post('/login','ApiAuth\AuthController@logIn')->middleware('check.age');
Route::middleware('auth:sanctum')->delete('/logout','ApiAuth\AuthController@logOut');

//Admins
//can--admin:admin
Route::middleware('auth:sanctum')->get('users/abilities','ApiAuth\AuthController@abi');
Route::middleware('auth:sanctum')->get('users/{user}/abilities','ApiAuth\AuthController@showAbi');
Route::middleware('auth:sanctum')->post('users/{user}/abilities','ApiAuth\AuthController@grantAbi');
Route::middleware('auth:sanctum')->delete('users/{user}/abilities','ApiAuth\AuthController@revokeAbi');

Route::middleware('auth:sanctum')->put('/users/{user}','UserController@update');
Route::middleware('auth:sanctum')->delete('/users/{user}','UserController@destroy');

Route::middleware('auth:sanctum')->get('/posts/comments','CommentController@allPC');
Route::middleware('auth:sanctum')->get('/comments','CommentController@all');
Route::middleware('auth:sanctum')->get('/comments/{comment}','CommentController@show');

Route::middleware('auth:sanctum')->delete('/posts','PostController@destroyAll');
Route::middleware('auth:sanctum')->delete('/comments','CommentController@destroyAll');
Route::middleware('auth:sanctum')->delete('/posts/{post}/comments','CommentController@destroyFromPost');


//Logged-in Users
//can--user:info
Route::middleware('auth:sanctum')->get('/users','UserController@all');
Route::middleware('auth:sanctum')->get('/users/{user}','UserController@show');

//can--user:profile
Route::middleware('auth:sanctum')->post('/profile','UserController@store');
Route::middleware('auth:sanctum')->put('/profile','UserController@update');

//can--post:publish
Route::middleware('auth:sanctum')->post('/posts','PostController@store');
//can--post:edit
Route::middleware('auth:sanctum')->put('/posts/{post}','PostController@update');
//can--post:delete
Route::middleware('auth:sanctum')->delete('/posts/{post}','PostController@destroy');

//can--com:publish
Route::middleware('auth:sanctum')->post('/posts/{post}/comments','CommentController@store');
//can--com:edit
Route::middleware('auth:sanctum')->put('/posts/{post}/comments/{comment}','CommentController@update');
//can--com:delete
Route::middleware('auth:sanctum')->delete('/posts/{post}/comments/{comment}','CommentController@destroy');

//All Users
Route::get('/posts','PostController@all');
Route::get('/posts/{post}','PostController@show');

Route::get('/posts/{post}/comments','CommentController@allFromPost');
Route::get('/posts/{post}/comments/{comment}','CommentController@showFromPost');







