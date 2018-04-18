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
            if(isset($request['recomm'])) {
                $ret = get_id_by_url_encode($request['recomm']);
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
}
