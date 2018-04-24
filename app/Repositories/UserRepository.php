<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    protected $user;
    
    public function __construct()
    {
        $this->user = new User();
    }

    public function create($request)
    {
        $user = [
            'telephone' => $request['telephone'],
            'password' => bcrypt($request['password']),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        if($this->getUserByPhone($request['telephone'])) {
            return '该手机号码已注册!';
        }else {
            $key = config('my.site.recomm');
            if(isset($request[$key])) {
                $ret = get_id_by_url_encode($request[$key]);
                $types = config('my.site.recomm_types');
                if($ret && in_array($ret['key'], $types) && $ret['val'] > 0) {
                    $find = DB::table($ret['key'])->where('id', $ret['val'])->first();
                    if($find) {
                        $user['recomm_type'] = array_search($ret['key'], $types);
                        $user['recomm_id'] = $ret['val'];
                    }
                }
            }
        }
        $ret = $this->user->insert($user);
        return $ret ? null : '添加用户失败!';
    }

    public function getUserByPhone($phone)
    {
        return $this->user->where('telephone', $phone)->first();
    }

    public function check_password($user_id, $password)
    {
        $_password = $this->user->select('password')->where('id', $user_id)->value('password');
        return Hash::check($password, $_password);
    }

    public function update_info_by_id($user_id, Array $data)
    {
        return $this->user->where('id', $user_id)->update($data);
    }

    public function update_info_by_telephone($telephone, Array $data)
    {
        return $this->user->where('telephone', $telephone)->update($data);
    }

    public function activate($user_id)
    {
        return $this->user->where('id', $user_id)
            ->where('activated_at', NULL)
            ->update([
                'activated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function getCountByToday()
    {
        $activates = $this->user->whereRaw('recomm_id IS NOT NULL')->where('activated_at', '>', date('Y-m-d'))->count();
        $recomms = $this->user->whereRaw('recomm_id IS NOT NULL')->where('created_at', '>', date('Y-m-d'))->count();
        $no_recomms = $this->user->where('recomm_id', null)->where('created_at', '>', date('Y-m-d'))->count();
        return [
            'activate' => $activates,
            'register' => $recomms . '+' . $no_recomms,
        ];
    }

    public function getCountByDate($date, $date_key = 'created_at')
    {
        return $this->user->where($date_key, '>', $date)->get()->toArray();
    }
}
