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
        $datas = BannerResource::collection($this->banner->getList($request->all()));
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

    public function checkError(&$datas, $file = null)
    {
        if($datas['start_time'] > $datas['end_time']) {
            return '起始时间不能大于结束时间!';
        }else if(isset($datas['app_id']) && !$this->banner->checkAppId($datas['app_id'])) {
            return '无效的APP id!';
        }else  {
            if($file) {
                $datas['image'] = $file->store('banner', 'public');
                if(!$datas['image']) return '保存图片失败';
            }
        }
        return null;
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
        $file = $request->hasFile('image') ? $request->file('image') : null;
        $error = $this->checkError($datas, $file);
        if(!$file) {
            $this->set_error('图片不能为空');
        }else if(!$error) {
            $ret = $this->banner->create($datas);
            if($ret) $this->set_success('添加成功');
            else $this->set_error('添加失败');
        }else {
            $this->set_error($error);
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
    public function update(StoreBannerPost $request)
    {
        $datas = $request->all();
        if(!isset($datas['id'])) {
            $this->set_error('缺少参数');
        }else {
            $file = $request->hasFile('image') ? $request->file('image') : null;
            $error = $this->checkError($datas, $file);
            if(!$error) {
                $ret = $this->banner->update($datas);
                if($ret) $this->set_success('更新成功');
                else $this->set_error('更新失败');
            }else {
                $this->set_error($error);
            }
        }
        return response()->json($this->get_result());
    }

    public function updateStatus(Request $request)
    {
        $id = $request->get('id');
        $status = $request->get('status');
        if($id) {
            $status = (bool)$status;
            $ret = $this->banner->updateStatus($id, $status);
            if($ret) {
                $str = $status ? '成功开启' : '成功关闭';
                $this->set_success($str)->set_data('status', $status);
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
