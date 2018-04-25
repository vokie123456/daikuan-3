<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryPost;

class CategoryController extends Controller
{
    protected $category;
    
    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

    /**
     * app列表.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $datas = CategoryResource::collection($this->category->getList($request->all()));
        $this->set_success('获取成功')->set_data('category', $datas);
        return response()->json($this->get_result());
    }

    public function getAllToGroup()
    {
        $result = $this->category->getAll();
        $datas = [];
        $types = config('my.site.moudle_type');
        foreach($result as $key => $val) {
            if(!isset($datas[$val['type']])) {
                $datas[$val['type']] = [
                    'name' => $types[$val['type']],
                    'child' => [],
                ];
            }
            $datas[$val['type']]['child'][] = [
                'id' => $val['id'],
                'name' => $val['name'],
            ];
        }
        $this->set_success('获取成功')->set_data('category', array_values($datas));
        return response()->json($this->get_result());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryPost $request)
    {
        //
        $datas = $request->all();
        if($request->hasFile('image')) {
            $datas['image'] = $request->file('image')->store('category', 'public');
        }
        $ret = $this->category->create($datas);
        if($ret) $this->set_success('添加成功')->set_data('ret', $ret);
        else $this->set_error('添加失败');
        return response()->json($this->get_result());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id) {
            $category = $this->category->getById($id);
            if($category) {
                $result = new CategoryResource($category);
                $this->set_success('获取成功')->set_data('category', $result);
            }else {
                $this->set_error('获取失败');
            }
        }else {
            $this->set_error('缺少参数');
        }
        return response()->json($this->get_result());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoryPost $request)
    {
        //
        $datas = $request->all();
        if($request->hasFile('image')) {
            $datas['image'] = $request->file('image')->store('category', 'public');
        }
        if(!isset($datas['id'])) {
            $this->set_error('缺少参数');
        }else {
            $ret = $this->category->update($datas);
            if($ret) $this->set_success('更新成功')->set_data('ret', $ret);
            else $this->set_error('更新失败');
        }
        return response()->json($this->get_result());
    }

    public function updateStatus(Request $request)
    {
        $id = $request->get('id');
        $status = $request->get('status');
        if($id) {
            $status = (bool)$status;
            $ret = $this->category->updateStatus($id, $status);
            if($ret) {
                $str = $status ? '成功开启' : '成功关闭';
                $this->set_success($str)->set_data('status', $status);
            } else $this->set_error('更新失败');
        }else {
            $this->set_error('缺少参数');
        }
        return response()->json($this->get_result());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!$id) {
            $this->set_error('缺少参数');
        }else if($this->category->checkDelete($id)) {
            $this->set_error('该类别下存在关联APP, 无法删除!');
        }else {
            $ret = $this->category->delete($id);
            if($ret) $this->set_success('删除成功')->set_data('ret', $ret);
            else $this->set_error('删除失败');
        }
        return response()->json($this->get_result());
    }
}
