<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'telephone' => $this->telephone,
            'name' => $this->name,
            'sex' => $this->sex,
            'sex_name' => config('my.site.sexs')[$this->sex],
            'telephone' => $this->telephone,
            'birthday' => $this->birthday ? substr($this->birthday, 0, 10) : '',
            'email' => $this->email,
            'profession' => $this->profession,
            'address' => $this->address,
        ];
    }
}
