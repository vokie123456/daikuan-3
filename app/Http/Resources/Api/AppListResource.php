<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AppListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $rate_types = config('my.site.rate_types');
        $moneys = json_decode($this->moneys, true);
        $terms = json_decode($this->terms, true);
        $marks = isset($this->marks) ? json_decode($this->marks, true) : null;
        $term_rand = $terms[0]['value'] . $terms[0]['type'];
        if(count($terms) > 1) {
            $last_term = $terms[count($terms) - 1];
            $term_rand .= ('-' . $last_term['value'] . $last_term['type']);
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => url(config('my.site.storage') . $this->icon),
            'money_max' => max($moneys),
            'money_rand' => $this->get_rand_string($moneys, true),
            'term_rand' => $term_rand,
            'apply_number' => $this->apply_number,
            'synopsis' => $this->synopsis,
            'rate' => floatval($this->rate),
            'rate_type_name' => $rate_types[$this->rate_type],
            'marks' => ($marks && !empty($marks)) ? $marks[0] : '',
            'isNew' => isset($this->isNew) ? $this->isNew : 0,
        ];
    }

    public function get_rand_string(Array $data, $sort = false)
    {
        $str = '';
        if(!empty($data)) {
            if($sort) sort($data);
            $str = $data[0];
            if(count($data) > 1) {
                $str .= ('-' . $data[count($data) - 1]);
            }
        }
        return $str;
    }
}
