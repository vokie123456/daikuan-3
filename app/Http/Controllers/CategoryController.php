<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
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
        // DB::enableQueryLog();
        $datas = CategoryResource::collection($this->category->getList($request->all()));
        // error_log(print_r(DB::getQueryLog(), true));
        $this->set_success('获取成功')->set_data('category', $datas);
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
        $category = $this->category->getById($id);
        if($id) {
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
        if($request->get('id')) {
            $ret = $this->category->updateStatus($request->get('id'), $request->get('status'));
            if($ret) $this->set_success('更新成功')->set_data('ret', $ret);
            else $this->set_error('更新失败');
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
        }else {
            $ret = $this->category->delete($id);
            if($ret) $this->set_success('删除成功')->set_data('ret', $ret);
            else $this->set_error('删除失败');
        }
        return response()->json($this->get_result());
    }
}
