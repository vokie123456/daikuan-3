@extends('agent.layouts.site')

@section('content')

<div class="tablebox">
    <div id="toolbar"></div>
    <!-- data-toggle="table" -->
    <table id="mytab" class="table table-hover" data-url="{{ url('agents/promotedata') }}">
        <thead>
            <tr>
                <th data-field="order_no" data-formatter="formatName">手机</th>
                <th data-field="type" data-formatter="formatType">姓名</th>
                <th data-field="oPrice">推荐方</th>
                <th data-field="name">是否激活</th>
                <th data-field="oUserName" data-formatter="formatOrderUser">激活时间</th>
                <th data-field="rRealname1">添加时间</th>
            </tr>
        </thead>
    </table>
</div>

@endsection