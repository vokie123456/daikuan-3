<?php
namespace App\Repositories;

use App\Models\Setting;

class SettingRepository
{
    protected $setting;
    
    public function __construct(Setting $setting) {
        $this->setting = $setting;
    }

    protected function _unserialize_value($value, $default = '') {
        if(!isset($value)) return $default;
        return unserialize(stripcslashes($value));
    }

    //获取设置信息
    public function getSettings($type = 'basic') {
        $ret = $this->setting->where('type', $type)->get()->toArray();
        $result = [];
        if($ret) {
            foreach($ret as $key => $val) {
                $result[$val['code']] = $val['serialize'] == 1 ? $this->_unserialize_value($val['value']) : $val['value'];
            }
        }
        return $result;
    }

    //设置设置信息
    public function setSettings(Array $datas, $type) {
        $_datas = [];
        foreach($datas as $key => $val) {
            $item = [
                'code' => $key,
                'type' => $type,
            ];
            if(is_array($val)) {
                $item['value'] = serialize($val);
                $item['serialize'] = 1;
            }else {
                $item['value'] = $val;
                $item['serialize'] = 0;
            }
            $_datas[] = $item;
        }
        $this->setting->where('type', $type)->delete();
        $this->setting->insert($_datas);
    }
}
