@extends('agent.layouts.site')

@section('content')

<div id="qrcode"></div>

<div style="margin-top: 40px;">
    <input type="text" class="form-control" value="{{ $myurl }}" />
</div>

<script type="text/javascript">
    new QRCode(document.getElementById("qrcode"), "{{ $myurl }}");
</script>
@endsection