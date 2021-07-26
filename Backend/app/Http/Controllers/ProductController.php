<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    function post(Request $request)
    {
        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->active = $request->active;
        $product->img = $request->img;
        $product->category_id = $request->category_id;


        $product->save();

        return response()->json(
            [
                "message" => "Success",
                "data" => $product
            ]
        );
    }

    function get(Request $request)
    {
        // dd($request);

        if ($request->category_name) {
            $data = DB::table('products as p')->join('categories as c', 'p.category_id', '=', 'c.id')
                ->select('p.id', 'p.name', 'p.price', 'p.active', 'p.img', 'p.category_id', 'c.name as category_name')
                ->where('c.name', $request->category_name)
                ->get();
        } else {
            $data = DB::table('products as p')->join('categories as c', 'p.category_id', '=', 'c.id')
                ->select('p.id', 'p.name', 'p.price', 'p.active', 'p.img', 'p.category_id', 'c.name as category_name')
                ->get();
        }

        // $data = Product::all();

        $dataResult = [];

        foreach ($data as $i => $value) {
            $dataResult[$i] = [
                'id'    => $value->id,
                'name'    => $value->name,
                'price'    => $value->price,
                'active'    => $value->active,
                'img'    => $value->img,
                'category'  => [
                    'id' => $value->category_id,
                    'name' => $value->category_name
                ]
            ];
        }


        return response()->json(
            [
                "message" => "Success!",
                "data" => $dataResult
            ]
        );
    }
    function getById($id)
    {
        $data = Product::where('id', $id)->get();

        return response()->json(
            [
                "message" => "Success!",
                "data" => $data
            ]
        );
    }


    function put($id, Request $request)
    {
        $product = Product::where('id', $id)->first();
        if ($product) {
            $product->name = $request->name ? $request->name : $product->name;
            $product->price = $request->price ? $request->price : $product->price;
            $product->quantity = $request->quantity ? $request->quantity : $product->quantity;
            $product->active = $request->active ? $request->active : $product->active;

            $product->save();
            return response()->json(
                [
                    "message" => "PUT Method Success! ",
                    "data" => $product
                ]
            );
        }
        return response()->json(
            [
                "message" => "Product with id " . $id . " is not found."
            ],
            400
        );
    }

    function delete($id) //saran menggunakan soft deleted
    {
        $product = Product::where('id', $id)->first();
        if ($product) {
            $product->delete();
            return response()->json(
                [
                    "message" => "DELETE Product " . $id . " Success!"
                ]
            );
        }
        return response()->json(
            [
                "message" => "Product with id " . $id . " is not found."
            ],
            400
        );
    }
}
