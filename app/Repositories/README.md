# 资料库


> 完成控制器与模型(数据表)中间的逻辑

`在构造函数处导入模型, 如:`

```PHP
namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
    protected $company;
    
    public function __construct(Company $company)
    {
        $this->company = $company;
    }
}

```