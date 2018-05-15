<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AgentRepository;

class AgentController extends Controller
{
    protected $agent;
    //
    public function myurl(Request $request)
    {
        $this->agent = session('agent');
        if($this->agent) {
            $share_url = config('my.site.register_path');
            $recom_key = config('my.site.recomm');
            $code = create_url_encode_by_id('agents', $this->agent->id);
            $myurl = $share_url . "?{$recom_key}=" . $code;
            $headjs[] = asset('js/qrcode.min.js');
            return view('agent/myurl', compact('myurl', 'headjs'));
        }else {
            return redirect('agents');
        }
    }

    public function promotes(Request $request)
    {
        $this->agent = session('agent');
        if($this->agent) {
            return view('agent/promote', [
                'stylesheets' => [
                    asset('bootstrap-table/bootstrap-table.css'),
                ],
                'headjs' => [
                    asset('bootstrap-table/bootstrap-table.js'),
                    asset('bootstrap-table/bootstrap-table-zh-CN.js'),
                ],
                'javascripts' => [
                    asset('js/utils.js'),
                    asset('js/promote.js'),
                ],
            ]);
        }else {
            return redirect('agents');
        }
    }

    public function getPromoteDatas(Request $request)
    {
        $this->agent = session('agent');
        if($this->agent) {
            return response()->json([
                'total' => 0,
                'rows' => null,
            ]);
        }else {
            return response('你还未登录', 403);
        }
    }
}
