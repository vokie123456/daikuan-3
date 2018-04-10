<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CategoryResource extends JsonResource
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
        $types = ['首页', '首页活动', '贷款', '秒放款'];
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'type_name' => $types[$this->type],
            'image' => $image,
            'sort' => $this->sort,
            'sort_app' => $this->sort_app,
            'status' => $this->status ? true : false,
            'created_at' => $this->created_at,
        ];
    }
}
