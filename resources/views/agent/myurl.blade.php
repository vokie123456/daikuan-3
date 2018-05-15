@extends('agent.layouts.site')

@section('content')

<p style="margin-bottom: 30px;">{{ $myname }}</p>

<div id="qrcode"></div>

<div style="margin-top: 30px;">
    <input type="text" class="form-control" value="{{ $myurl }}" />
</div>

<script type="text/javascript">
    new QRCode(document.getElementById("qrcode"), "{{ $myurl }}");
</script>
@endsection