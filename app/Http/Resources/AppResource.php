<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $icon = ($this->icon && Storage::disk('public')->exists($this->icon)) ? 
            Storage::url($this->icon) : asset('images/no_image.png');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'weburl' => $this->weburl,
            'appicon' => $icon,
            'note' => $this->note,
            // 'company_id' => $this->company_id,
            // 'company_name' => $this->company ? $this->company->name : '',
            'synopsis' => $this->synopsis,
            'details' => $this->details,
            'rates' => [
                'value' => $this->rate,
                'type' => $this->rate_type,
            ],
            'moneys' => json_decode($this->moneys, true),
            'terms' => json_decode($this->terms, true),
            'marks' => json_decode($this->marks, true),
            'repayments' => json_decode($this->repayments, true),
            'apply_number' => $this->apply_number,
            'sort' => $this->sort,
            'recommend' => round(intval($this->recommend) / 2, 1),
            'status' => $this->status ? true : false,
            'isNew' => $this->isNew ? true : false,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
