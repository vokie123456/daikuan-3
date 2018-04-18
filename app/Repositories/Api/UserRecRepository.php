<?php
namespace App\Repositories\Api;

use App\Models\User_record;

class UserRecRepository
{
    protected $user_record;
    
    public function __construct(User_record $user_record)
    {
        $this->user_record = $user_record;
    }

    public function addRecord($app_id, $user_id)
    {
        $this->user_record->where('app_id', $app_id)->where('user_id', $user_id)
            ->where('created_at', '>', date('Y-m-d'))->delete();
        $this->user_record->insert(compact('app_id', 'user_id'));
    }

    public function getRecords($user_id)
    {
        $appRepository = new AppRepository();
        $appids = $this->user_record->where('user_id', $user_id)->pluck('app_id')->toArray();
        return $appRepository->getAppByInId($appids);
    }
}
