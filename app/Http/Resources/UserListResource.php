<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'telephone' => $this->telephone,
            'name' => $this->name,
            'recomm_type' => $this->recomm_type,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'is_activate' => isset($this->activated_at) ? true : false,
        ];
    }
}
