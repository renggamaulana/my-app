<?php

namespace App\Http\Controllers;

use App\Models\ShopSetting;
use Illuminate\Http\Request;

class ShopSettingController extends Controller
{
    public function index()
    {
        $settings = ShopSetting::first() ?? ShopSetting::create([
            'name' => 'Corndog Alle',
            'address' => 'Jl. Cilandak, No 45, Jakarta Barat, DKI Jakarta 94107',
            'phone' => '(+62) 895-3653-05896',
            'email' => 'info@corndogalle.com',
        ]);

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
        ]);

        $settings = ShopSetting::first() ?? new ShopSetting();
        $settings->name = $request->name;
        $settings->address = $request->address;
        $settings->phone = $request->phone;
        $settings->email = $request->email;
        $settings->save();

        return response()->json($settings);
    }
}
