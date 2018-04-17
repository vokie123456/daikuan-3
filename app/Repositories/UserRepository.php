<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    protected $user;
    
    public function __construct()
    {
        $this->user = new User();
    }

    public function create(Array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->user->insert($data);
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
