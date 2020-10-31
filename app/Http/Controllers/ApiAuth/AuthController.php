<?php

namespace App\Http\Controllers\ApiAuth;

use App\User;
use App\Ability;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function signUp(Request $request){
        $request -> validate([
            'email' => 'email|required|unique:users',
            'name' => 'required|min:1|max:30',
            'age' => 'required|max:999',
            'password' => 'required|min:6'
        ]);
        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'age' => $request->age,
            'password' => Hash::make($request->password)]);
        if($user){
            $user->abilities()->attach(Ability::where('name','user:profile')->first());
            $user->abilities()->attach(Ability::where('name','post:publish')->first());
            $user->abilities()->attach(Ability::where('name','com:publish')->first());
            return response()->json($user, 201);
        }
        return abort(400, "Something went wrong...");
    }

    public function logIn(Request $request){
        $request -> validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user||!Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $abilities = $user->abilities;
        foreach ($abilities as $ability){
            $ab_array[] = $ability->name;
        }
        $token = $user->createToken($request->email, $ab_array)->plainTextToken;
        return response()->json(['token' => $token],201);
    }

    public function logOut(Request $request){
        return response()->json(['closed_sessions' => $request->user()->tokens()->delete()],200);
    }

    public function abi(Request $request)
    {
        if($request->user()->tokenCan('admin:admin')){
            $abilities = Ability::all();
            return response()->json($abilities, 200);
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }

    public function showAbi(Request $request, int $user)
    {
        if($request->user()->tokenCan('admin:admin')){
            $user_sel = User::find($user);
            if($user_sel){
                $abilities = $user_sel->abilities;
                return response()->json($abilities, 200);
            }else{
                return response()->json(['message' => "The user you are looking for doesn't exists.",
                                      'code' => 404], 404);
            }
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }

    public function grantAbi(Request $request, int $user){
        if($request->user()->tokenCan('admin:admin')){
            $user_sel = User::find($user);
        if($user_sel){
            $request -> validate([
                'ability_name' => 'required'
            ]);
            $ability = $request->ability_name;
            if(!Ability::where('name', $ability)->first()){
                return response()->json(['message' => "This ability doesn't exists.",
                                        'code' => 200],200);
            }
            if($user_sel){
                $rep_ability = $user_sel->abilities->where('name', $ability)->first();
                if(!$rep_ability){
                    $user_sel->abilities()->attach(Ability::where('name', $ability)->first());
                    return response()->json(['message' => $ability." ability granted to the user ".$user,
                                        'code' => 200],200);
                }
                return response()->json(['message' => "This ability has already been granted to this user.",
                                        'code' => 200],200);
            }
        }else{
            return response()->json(['message' => "The user you are looking for doesn't exists.",
                                      'code' => 404], 404);
        }
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }

    public function revokeAbi(Request $request, $user){
        if($request->user()->tokenCan('admin:admin')){
            $selected_user = User::find($user);
        if($selected_user){
            $request -> validate([
                'ability_name' => 'required'
            ]);
            $ability = $request->ability_name;
            if(!Ability::where('name', $ability)->first()){
                return response()->json(['message' => "This ability doesn't exists.",
                                        'code' => 200],200);
            }
            if($selected_user){
                $ability_exs = $selected_user->abilities->where('name', $ability)->first();
                if($ability_exs){
                    $selected_user->abilities()->detach(Ability::where('name', $ability)->first());
                    return response()->json(['message' => $ability." ability revoked for the user ".$user,
                                        'code' => 200],200);
                }
                return response()->json(['message' => "This ability hasn't been granted to this user.",
                                        'code' => 200],200);
            }
        }else{
            return response()->json(['message' => "The user you are looking for doesn't exists.",
                                      'code' => 404], 404);
        }
        }else{
            return response()->json(['message' => "Unauthorized",
                                      'code' => 401], 401);
        }
    }
}
