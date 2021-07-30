<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // mengubah nama key pada respon json
        // return [
        //     'id' => $this->id,
        //     'nama' => $this->name,
        //     'harga' => $this->price
        // ];
    }
}
