<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    function get()
    {
        $data = DB::table('categories')->select('id', 'name')->get();
        return response()->json(
            [
                "message" => "Success!",
                "data" => $data
            ]
        );
    }

    function post()
    {
        return response()->json(
            [
                "message" => "Success!",
            ]
        );
    }

    function put()
    {
        return response()->json(
            [
                "message" => "Success!",
            ]
        );
    }

    function delete()
    {
        return response()->json(
            [
                "message" => "Success!",
            ]
        );
    }
}
