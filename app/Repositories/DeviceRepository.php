<?php
namespace App\Repositories;

use App\Models\Device;
use Illuminate\Support\Facades\Auth;

class DeviceRepository
{
    protected $device;
    
    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function getDeviceByUniqueId($uniqueid)
    {
        return $this->device->where('unique_id', $uniqueid)->first();
    }

    public function saveDeviceInfo($datas)
    {
        if(isset($datas['name'], $datas['unique_id'])) {
            $row = $this->getDeviceByUniqueId($datas['unique_id']);
            if(!$row) {
                $row = new Device();
                $row->unique_id = $datas['unique_id'];
                $row->created_at = date('Y-m-d H:i:s');
            }

            $user = Auth::guard('api')->user();
            if($user) $row->user_id = $user->id;

            $row->name = $datas['name'];
            $row->operator = isset($datas['operator']) ? $datas['operator'] : '';
            $row->network_type = isset($datas['network_type']) ? $datas['network_type'] : '';
            $row->model = isset($datas['model']) ? $datas['model'] : '';
            $row->phone_model = isset($datas['phone_model']) ? $datas['phone_model'] : '';
            $row->sys_name = isset($datas['sys_name']) ? $datas['sys_name'] : '';
            $row->phone_sys_version = isset($datas['phone_sys_version']) ? $datas['phone_sys_version'] : '';
            $row->request_ip = request()->getClientIp();
            $row->updated_at = date('Y-m-d H:i:s');
            return $row->save();
        }
        return false;
    }
}
