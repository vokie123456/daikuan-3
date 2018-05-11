<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $share_url = config('my.site.register_path');
        $key = config('my.site.recomm');
        $code = create_url_encode_by_id('companies', $this->id);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'share_url' => $share_url . "?{$key}=" . $code,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
