@extends('agent.layouts.site')

@section('content')
<form method="POST" action="{{ route('createAgentForm') }}" class="form-horizontal">
    @csrf

    <div class="form-group">
        <label for="name" class="col-sm-2 control-label">名称</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="name" id="name" />
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="col-sm-2 control-label">登录名</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="username" id="username" />
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-sm-2 control-label">登录密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="password" id="password" />
        </div>
    </div>
    <div class="form-group">
        <label for="password_confirmation" class="col-sm-2 control-label">确认密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" />
        </div>
    </div>
    <div class="form-group">
        <label for="note" class="col-sm-2 control-label">备注</label>
        <div class="col-sm-10">
            <textarea class="form-control" rows="8" name="note" id="note"></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">添 加</button>
        </div>
    </div>
</form>
@endsection