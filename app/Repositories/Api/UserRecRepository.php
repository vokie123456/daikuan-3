<?php
namespace App\Repositories\Api;

use App\Models\User_record;
use App\Http\Resources\Api\AppListResource;
use Illuminate\Support\Facades\Log;

class UserRecRepository
{
    protected $user_record;
    
    public function __construct(User_record $user_record)
    {
        $this->user_record = $user_record;
    }

    public function click_promote($app_id, $user_id)
    {
        $record = $this->user_record->where('app_id', $app_id)->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')->first();
        if($record && $record['click_promote'] == 0) {
            return $this->user_record->where('id', $record['id'])->update([
                'click_promote' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return false;
    }

    public function addRecord($app_id, $user_id)
    {
        $record = [
            'app_id' => $app_id,
            'user_id' => $user_id,
            'ip' => request()->getClientIp(),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->user_record->insert($record);
    }

    public function getRecords($user_id)
    {
        $datas = $this->user_record->select('apps.*')
            ->where('user_records.user_id', $user_id)
            ->leftJoin('apps', 'apps.id', '=', 'user_records.app_id')
            ->orderBy('user_records.created_at', 'desc')->simplePaginate(15);
        $_datas = $datas->toArray();
        if(count($_datas['data'])) {
            return [
                'data' => AppListResource::collection($datas),
                'current_page' => $_datas['current_page'],
                'per_page' => $_datas['per_page'],
            ];
        }
        return null;
    }
}
