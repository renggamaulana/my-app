<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
// use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        $response =  [
            'success' => true,
            // 'data' => ProductResource::collection($products) //Jika menggunakan resource
            'data' => $products,
            'message' => "Products Successfully Retrieved"
        ];
        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'price' => 'required',
            'img' => 'required',
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response =  [
                'success' => true,
                'message' => $validator->errors()
            ];

            return response()->json($response, 403);
        }

        $product = Product::create($input);

        $response =  [
            'success' => true,
            'data' => $product,
            'message' => "Products Successfully Created"
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            $response =  [
                'success' => false,
                'message' => "Products Not Found."
            ];

            return response()->json($response, 403);
        }

        $response =  [
            'success' => true,
            'data' => $product,
            'message' => "Product Successfully Retrieved"
        ];

        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'price' => 'required',
            'img' => 'required',
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response =  [
                'success' => true,
                'message' => $validator->errors()
            ];

            return response()->json($response, 403);
        }

        $product->name = $input['name'];
        $product->price = $input['price'];
        // $product->active = $input['active'];
        $product->img = $input['img'];
        $product->category_id = $input['category_id'];

        $product->save();

        $response =  [
            'success' => true,
            'data' => $product,
            'message' => "Product Successfully Updated"
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        $response =  [
            'success' => true,
            'data' => [],
            'message' => "Product Successfully Deleted"
        ];

        return response()->json($response, 200);
    }
}
