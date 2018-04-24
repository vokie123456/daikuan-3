<?php
namespace App\Repositories\Api;

use App\Models\Promote;

class PromoteRepository
{
    protected $promote;
    
    public function __construct(Promote $promote)
    {
        $this->promote = $promote;
    }

    public function click_promote($app_id, $user_id)
    {
        $row = $this->promote->where('app_id', $app_id)
            ->where('user_id', $user_id)
            ->where('created_at', '>', date('Y-m-d'))->first();
        if(!$row) {
            $this->promote->insert(compact('app_id', 'user_id'));
        }
    }

    public function getCountByToday()
    {
        return $this->promote->where('created_at', '>', date('Y-m-d'))->count();
    }

    public function getCountByDate($date)
    {
        return $this->promote->where('created_at', '>', $date)->get()->toArray();
    }
}
