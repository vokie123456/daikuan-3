<?php

namespace App\Http\Resources\Api;

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
            url(config('my.site.storage') . $this->image) : asset('images/no_image.png');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'image' => $image,
            'app_id' => $this->app_id ? $this->app_id : 0,
            'url' => $this->url,
        ];
    }
}
