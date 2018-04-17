<?php
namespace App\Repositories;

use App\Models\User;

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
}
