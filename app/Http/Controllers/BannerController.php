<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BannerRepository;
use App\Http\Requests\StoreBannerPost;
use App\Http\Resources\BannerResource;

class BannerController extends Controller
{
    protected $banner;
    
    public function __construct(BannerRepository $banner)
    {
        $this->banner = $banner;
    }

    /**
     * app列表.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // DB::enableQueryLog();
        $datas = BannerResource::collection($this->banner->getList($request->all()));
        // error_log(print_r(DB::getQueryLog(), true));
        $this->set_success('获取成功')->set_data('banners', $datas);
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
            $banner = $this->banner->getById($id);
            if($banner) {
                $result = new BannerResource($banner);
                $this->set_success('获取成功')->set_data('banner', $result);
            }else {
                $this->set_error('获取失败');
            }
        }else {
            $this->set_error('缺少参数');
        }
        return response()->json($this->get_result());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBannerPost $request)
    {
        //
        $datas = $request->all();
        if(isset($datas['app_id']) && !$this->banner->checkAppId($datas['app_id'])) {
            $this->set_error('无效的APP id!');
        }else {
            if($request->hasFile('image')) {
                $datas['image'] = $request->file('image')->store('banner', 'public');
            }
            $ret = $this->banner->create($datas);
            if($ret) $this->set_success('添加成功')->set_data('ret', $ret);
            else $this->set_error('添加失败');
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
    public function update(Request $request)
    {
        $datas = $request->all();
        if($request->hasFile('image')) {
            $datas['image'] = $request->file('image')->store('banner', 'public');
        }
        if(!isset($datas['id'])) {
            $this->set_error('缺少参数');
        }else {
            $ret = $this->banner->update($datas);
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
            $ret = $this->banner->updateStatus($id, $status);
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
        if(!$id) {
            $this->set_error('缺少参数');
        }else {
            $ret = $this->banner->delete($id);
            if($ret) $this->set_success('删除成功')->set_data('ret', $ret);
            else $this->set_error('删除失败');
        }
        return response()->json($this->get_result());
    }
}