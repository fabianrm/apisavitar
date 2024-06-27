<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BoxCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'data' => $this->collection,
            'total' => count($this->collection)
        ];

        // return [
        //     'boxs' => $this->collection->map(function ($box) {

        //         return [
        //             'id' => $box->id,
        //             'name' => $box->name,
        //             'city' => $box->cities->name,
        //             'address' => $box->address,
        //             'reference' => $box->reference,
        //             'latitude' => $box->latitude,
        //             'longitude' => $box->longitude,
        //             'total_ports' => $box->total_ports,
        //             'available_ports' => $box->available_ports,
        //             'status' => $box->status,
        //         ];



        //     }),
        // ];
        //return parent::toArray($request);
    }
}
