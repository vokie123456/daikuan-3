@extends('agent.layouts.site')

@section('content')

<div class="tablebox">
    <div id="toolbar" class="form-inline">
        <select id="sel-parent" class="form-control">
            <option value="0">请选择代理商名称</option>
            @foreach($childs as $val)
                <option value="{{ $val->id }}">{{ $val->name }}</option>
            @endforeach
        </select>
        <select id="sel-activate" class="form-control">
            <option value="0">是否激活</option>
            <option value="1">已激活</option>
            <option value="2">未激活</option>
        </select>
        <input type="text" class="form-control" id="inputStartDate" placeholder="开始时间" />
        <input type="text" class="form-control" id="inputEndDate" placeholder="结束时间" />
        <button type="button" class="btn btn-primary marginLeft10" id="btnSearch">
            <i class="fa fa-search"></i> 搜索
        </button>
        <button type="button" class="btn btn-default" id="btnReset">
            <i class="fa fa-refresh"></i> 重置
        </button>
    </div>
    <div>
        <h4 id="register_total"></h4>
        <h4 id="activate_total"></h4>
        <p id="mark_text" class="help-block"></p>
    </div>
    <!-- data-toggle="table" -->
    <table id="mytab" class="table table-hover" data-url="{{ url('agents/promotedata') }}">
        <thead>
            <tr>
                <th data-field="telephone">手机</th>
                <th data-field="agentname">推荐人</th>
                <th data-field="activated_at" data-formatter="formatActivate">是否激活</th>
                <th data-field="activated_at" data-sortable="true">激活时间</th>
                <th data-field="created_at" data-sortable="true">添加时间</th>
            </tr>
        </thead>
    </table>
</div>

@endsection