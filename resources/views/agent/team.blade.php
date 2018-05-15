@extends('agent.layouts.site')

@section('content')

<div style="margin-bottom: 20px;">
    <a href="/agents/create" class="btn btn-primary">添加</a>
</div>

<div class="tablebox">
    <div id="toolbar"></div>
    <!-- data-toggle="table" -->
    <table id="mytab" class="table table-hover" data-url="{{ url('agents/teamdata') }}">
        <thead>
            <tr>
                <th data-field="name" data-formatter="formatName">名称</th>
                <th data-field="share_url" data-formatter="formatUrl">推广链接</th>
                <th data-field="created_at">添加时间</th>
                <th data-field="register">注册人数</th>
                <th data-field="activate">激活人数</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    function formatName(value, row, index) {
        return row.name + '(' + row.username + ')';
    }

    function formatUrl(value, row, index) {
        return '<input type="text" class="form-control" value="' + value + '" />';
    }

    $('#mytab').bs_table({
        search: true,
	    searchPlaceholder: '输入名称搜索',
    });
</script>

@endsection