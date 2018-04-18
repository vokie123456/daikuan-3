<?php
namespace App\Repositories\Api;

use App\Models\User_collection;
use App\Repositories\Api\AppRepository;

class UserCollRepository
{
    protected $user_collection;
    
    public function __construct(User_collection $user_collection)
    {
        $this->user_collection = $user_collection;
    }

    public function checkUserIsCollection($app_id, $user_id)
    {
        return $this->user_collection->where('app_id', $app_id)->where('user_id', $user_id)->first();
    }

    public function toggleCollection($app_id, $user_id)
    {
        $result = [
            'msg' => '',
            'ret' => false,
        ];
        if($this->checkUserIsCollection($app_id, $user_id)) {
            $ret = $this->user_collection->where('app_id', $app_id)->where('user_id', $user_id)
                ->where('created_at', '>', date('Y-m-d'))->delete();
            if($ret) {
                $result['type'] = 0;
                $result['ret'] = true;
                $result['msg'] = '取消收藏成功';
            }else {
                $result['type'] = 1;
                $result['ret'] = false;
                $result['msg'] = '取消收藏失败';
            }
        }else {
            $ret = $this->user_collection->insert(compact('app_id', 'user_id'));
            if($ret) {
                $result['type'] = 1;
                $result['ret'] = true;
                $result['msg'] = '收藏成功';
            }else {
                $result['type'] = 0;
                $result['ret'] = false;
                $result['msg'] = '收藏失败';
            }
        }
        return $result;
    }

    public function getCollections($user_id)
    {
        $appRepository = new AppRepository();
        $appids = $this->user_collection->where('user_id', $user_id)->pluck('app_id')->toArray();
        return $appRepository->getAppByInId($appids);
    }
}
