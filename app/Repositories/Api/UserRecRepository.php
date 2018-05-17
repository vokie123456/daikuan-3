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
        $ip = request()->getClientIp();
        $sql = $this->user_record->where('app_id', $app_id)
            ->where('user_id', $user_id)
            ->where('created_at', '>', date('Y-m-d'));
        if($user_id) {
            $sql->delete();
        }else if($sql->where('ip', $ip)->count() >= config('my.site.same_ip_day_number')) {
            return;
        }
        $this->user_record->insert(compact('app_id', 'user_id', 'ip'));
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

    public function getCountByToday()
    {
        $members = $this->user_record->whereRaw('user_id IS NOT NULL')->where('created_at', '>', date('Y-m-d'))->count();
        $tourists = $this->user_record->where('user_id', null)->where('created_at', '>', date('Y-m-d'))->count();
        return $members + $tourists;
        return $members . '+' . $tourists;
    }

    public function getCountByDate($date)
    {
        return $this->user_record->where('created_at', '>', $date)->get()->toArray();
    }
}
