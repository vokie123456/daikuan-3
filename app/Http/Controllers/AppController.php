<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreAppPost;
use App\Repositories\AppRepository;
use App\Http\Resources\AppResource;

class AppController extends Controller
{
    protected $appRepository;

    public function __construct(AppRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }

    /**
     * app列表.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // DB::enableQueryLog();
        $datas = AppResource::collection($this->appRepository->getList($request->all()));
        // error_log(print_r(DB::getQueryLog(), true));
        $this->set_success('获取成功')->set_data('apps', $datas);
        return response()->json($this->get_result());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAppPost $request)
    {
        //
        $path = $request->file('appicon')->store('icon', 'public');
        if($path) {
            $datas = $request->all();
            $datas['appicon'] = $path;
            $ret = $this->appRepository->create($datas);
            if($ret) $this->set_success('添加成功')->set_data('ret', $ret);
            else $this->set_error('添加失败');
        }else {
            $this->set_error('图片存储失败');
        }
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
        //
        $app = $this->appRepository->getApp($id);
        if($app) {
            $result = new AppResource($app);
            $this->set_success('获取成功')->set_data('app', $result);
        }else {
            $this->set_error('获取失败');
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
    public function update(StoreAppPost $request)
    {
        //
        $datas = $request->all();
        if($request->hasFile('appicon')) {
            $datas['appicon'] = $request->file('appicon')->store('icon', 'public');
        }
        if($datas['appicon']) {
            $ret = $this->appRepository->update($datas);
            if($ret) $this->set_success('更新成功')->set_data('ret', $ret);
            else $this->set_error('更新失败');
        }else {
            $this->set_error('无图片数据');
        }
        return response()->json($this->get_result());
    }


    public function updateStatus(Request $request)
    {
        $id = $request->get('id');
        $status = $request->get('status');
        if($id) {
            $ret = $this->appRepository->updateStatus($id, $status);
            if($ret) {
                $str = $status ? '成功开启' : '成功关闭';
                $this->set_success($str)->set_data('ret', $ret);
            }else $this->set_error('更新失败');
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
        if($id) {
            $ret = $this->appRepository->destroy($id);
            if($ret) $this->set_success('删除成功')->set_data('ret', $ret);
            else $this->set_error('删除失败');
        }else {
            $this->set_error('缺少参数');
        }
        return response()->json($this->get_result());
    }
}
