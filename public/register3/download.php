
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title>APP下载</title>
	<style>
		body{
			background-color: #518ffb;
			margin: 0;
			padding: 0;
		}
		img{
			width: 100%;
			display: block;
		}
		#mcover {
		    position: fixed;
		    top: 0;
		    left: 0;
		    width: 100%;
		    height: 100%;
		    background: rgba(0, 0, 0, 0.7);
		    display: none;
		    z-index: 2000;
		}
		#mcover img {
		    position: fixed;
		    right: 18px;
		    top: 18px;
		    width: 80%;
		    z-index: 999;	
		}
		.btnDownload {
			display: inline-block;
			width: 60%;
			height: 42px;
			line-height: 42px;
			border: 1px solid #ccc;
			border-radius: 4px;
			font-size: 16px;
			margin-top: 20px;
			color: #fff;
			letter-spacing: 1px;
			cursor: pointer;
		}
		.btnBox {
			position: absolute;
			bottom: 50px;
			left: 0;
			right: 0;
			padding-bottom: 30px;
		}
	</style>
</head>

<?php
	$data = require_once(dirname(__DIR__) . '/version_info.php');
	$ios_url = '';
	$android_url = '';
	if(is_array($data)) {
		if(isset($data['ios'], $data['ios']['url'])) $ios_url = $data['ios']['url'];
		if(isset($data['android'], $data['android']['url'])) $android_url = $data['android']['url'];
	}
?>

<body>
	<script>
		var check_download_android = function(e) {
			var ua = window.navigator.userAgent.toLowerCase();
			if(ua.match(/MicroMessenger/i) == 'micromessenger') {
				document.getElementById('mcover').style.display='block';
			}else {
				e.target.href = "<?php echo $android_url;?>";
			}
		}
	</script>
	<div style="position: relative;">
		<img src="down_bg.jpg" alt="" />
		<div class="btnBox">
			<div style="text-align: center;">
				<a class="btnDownload" onclick="check_download_android(event);">Android下载</a>
			</div>
			<div style="text-align: center;">
				<a class="btnDownload" href="<?php echo $ios_url;?>">IOS下载</a>
			</div>
		</div>
	</div>
	
	<div id="mcover">
	     <img src="weixin_tip.png">
	</div>
	<script>  
	(function (doc, win) {
	        var docEl = doc.documentElement,
	            resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	            recalc = function () {
	                var clientWidth = docEl.clientWidth;
	                if (!clientWidth) return;
	                if(clientWidth>=1080){
	                    docEl.style.fontSize = '100px';
	                }else{
	                    docEl.style.fontSize = 100 * (clientWidth / 1080) + 'px';
	                }
	            };

	        if (!doc.addEventListener) return;
	        win.addEventListener(resizeEvt, recalc, false);
	        doc.addEventListener('DOMContentLoaded', recalc, false);
	    })(document, window);
	</script> 
</body>
</html>

                             