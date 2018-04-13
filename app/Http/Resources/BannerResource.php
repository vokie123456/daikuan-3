<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $image = ($this->image && Storage::disk('public')->exists($this->image)) ? 
            Storage::url($this->image) : asset('images/no_image.png');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'type' => $this->type,
            'image' => $image,
            'sort' => $this->sort,
            'app_id' => $this->app_id,
            'url' => $this->url,
            'start_time' => $this->start_time ? $this->start_time : '',
            'end_time' => $this->end_time ? $this->end_time : '',
            'status' => $this->status ? true : false,
        ];
    }
}
