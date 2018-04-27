<?php
namespace App\Repositories\Api;

use App\Models\User_collection;
use App\Repositories\Api\AppRepository;
use App\Http\Resources\Api\AppListResource;

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
            $ret = $this->user_collection->where('app_id', $app_id)->where('user_id', $user_id)->delete();
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
        $datas = $this->user_collection->select('apps.*')
            ->where('user_collections.user_id', $user_id)
            ->leftJoin('apps', 'apps.id', '=', 'user_collections.app_id')
            ->orderBy('user_collections.created_at', 'desc')->simplePaginate(15);
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
