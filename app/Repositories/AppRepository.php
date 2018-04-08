<?php
namespace App\Repositories;

use App\Models\App;

class AppRepository
{
    protected $appRepository;
    
    public function __construct(App $app)
    {
        $this->appRepository = $app;
    }

    public function create($datas)
    {
        $datas['rates'] = json_decode($datas['rates'], true);
        $app = array(
            'name' => $datas['name'],
            'weburl' => $datas['weburl'],
            'icon' => $datas['icon'],
            'company_id' => (int)$datas['company_id'],
            'synopsis' => $datas['synopsis'],
            'details' => $datas['details'],
            'rate' => (int)$datas['rates']['value'],
            'rate_type' => (int)$datas['rates']['type'],
            'moneys' => $datas['moneys'],
            'terms' => $datas['terms'],
            'repayments' => $datas['repayments'],
            'apply_number' => (int)$datas['apply_number'],
            'recommend' => intval(floatval($datas['recommend']) * 2),
            'status' => $datas['terms'] ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $this->appRepository::create($app);
    }
}