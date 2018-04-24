<?php 
/*
 |--------------------------------------------------------------------------
 | 图表类库
 |--------------------------------------------------------------------------
 | 
 |
 */

namespace App\Services;

//查询参数
class Echarts {
    protected $dayCount = 7;
    protected $days = [];
    protected $datas = [];
    protected $origin_datas = [];

    public function setDayCount($value){
        $value = intval($value);
        if($value > 0) $this->dayCount = $value;
        return $this;
    }

    public function getBeforeDate()
    {
        $timer = strtotime('-' . ($this->dayCount - 1) . ' days');
        return date('Y-m-d', $timer);
    }

    public function getDays($cache = true)
    {
        if(!empty($this->days) && $cache) return $this->days;
        $days = [date('Y-m-d')];
        for($i = 1; $i < $this->dayCount; $i++) {
            $timer = strtotime("-{$i} days");
            $days[] = date('Y-m-d', $timer);
        }
        sort($days);
        $this->days = $days;
        return $days;
    }

    public function addData(Array $data)
    {
        $this->datas[] = $data;
        $this->origin_datas[] = $data;
        return $this;
    }

    public function setData(Array $data, $date_key = 'created_at')
    {
        $ret = array_map(function($item) {return 0;}, array_flip($this->getDays()));
        foreach($data as $val) {
            $date = isset($val[$date_key]) ? substr($val[$date_key], 0, 10) : null;
            if($date && isset($ret[$date])) {
                $ret[$date] = $ret[$date] + 1;
            }
        }
        $this->datas[] = array_values($ret);
        $this->origin_datas[] = $ret;
        return $this;
    }

    public function getData($origin = false)
    {
        return $origin ? $this->origin_datas : $this->datas;
    }

    public function setSeriesData(Array $names)
    {
        foreach($this->datas as $key => $val) {
            $this->datas[$key] = [
                'name' => isset($names[$key]) ? $names[$key] : '',
                'data' => $val,
            ];
        }
        return $this;
    }
}
