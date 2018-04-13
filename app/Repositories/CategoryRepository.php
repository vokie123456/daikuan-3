<?php
namespace App\Repositories;

use App\Models\Category;
use App\Services\Formatquery;

class CategoryRepository
{
    protected $category;
    
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getById($id)
    {
        return $this->category->find($id);
    }

    public function getAll()
    {
        return $this->category->orderBy('created_at', 'asc')->get()->toArray();
    }

    public function getList($request = []) 
    {
        $config = array(
            'defSort'   => 'created_at',
            'defOrder'  => 'desc',
            'sortArr'   => array('created_at', 'name', 'status'),
            'searchArr' => array(
                'name'  => ['rule' => '%alias% like \'%%s%\'',],
                'type'  => [
                    'value'  => 0,
                    'allow'  => [-1, 0, 1],
                    'except' => [-1, 0],
                    'except_func' => function($val) {
                        if($val == 0) return '%alias% != 1';
                        if($val == -1) return 1;
                    },
                ],
            ),
        );
        $formatquery = new Formatquery($config);
        $query = $formatquery->setParams($request)->getParams();
        // error_log(print_r($query, true));
        return $this->category->orderBy($query['sort'], $query['order'])
                ->whereRaw($query['whereStr'] ? $query['whereStr'] : 1)
                ->skip($query['offset'])
                ->take($query['limit'])
                ->get();
    }

    public function format_data($datas)
    {
        $_data = array(
            'name' => $datas['name'],
            'type' => (int)$datas['type'],
            'image' => isset($datas['image']) ? $datas['image'] : '',
            'sort' => (int)$datas['sort'],
            'sort_app' => (int)$datas['sort_app'],
            'status' => (isset($datas['status']) && $datas['status']) ? 1 : 0,
        );
        return $_data;
    }

    public function create($datas)
    {
        $_data = $this->format_data($datas);
        $_data['created_at'] = date('Y-m-d H:i:s');
        return $this->category::create($_data);
    }

    public function update($datas)
    {
        $_data = $this->format_data($datas);
        $_data['image'] = rm_path_prev_storage($_data['image']);
        return $this->category->where('id', $datas['id'])->update($_data);
    }

    public function updateStatus($id, $status)
    {
        return $this->category
                ->where('id', $id)
                ->update(['status' => ($status ? 1 : 0)]);
    }

    public function delete($id)
    {
        return $this->category->where('id', $id)->delete();
    }
}
