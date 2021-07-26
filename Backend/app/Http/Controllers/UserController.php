<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    function get()
    {
        return response()->json(
            [
                "name" => "GET Method Success"
            ]
        );
    }
    function post(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;

        $user->save();

        return response()->json(
            [
                "name" => "POST Method Success",
                "data" => $user
            ]
        );
    }
    function put()
    {
        return response()->json(
            [
                "name" => "PUT Method Success"
            ]
        );
    }
    function delete()
    {

        return response()->json(
            [
                "name" => "DELETE Method Success"
            ]
        );
    }
}
