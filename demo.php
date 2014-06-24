<?php
header("Content-type: text/html; charset=utf-8"); 
$base_url= 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER["PHP_SELF"]);
// 空间名
$bucket = 'testxujif';

// 表单 API 验证密匙：需要访问又拍云管理后台的空间管理页面获取
$form_api_secret = 'MpFNdOcm2J0AieiGkB5GsW+7B4g=';
$options = array();
$options['bucket'] = $bucket;
// 授权过期时间：以页面加载完毕开始计时，10分钟内有效
$options['expiration'] = time()+600;
// 保存路径：最终将以"/年/月/日/upload_待上传文件名"的形式进行保存
$options['save-key'] = '/{year}/{mon}/{day}/upload_{filename}{.suffix}';

// 同步跳转 url：表单上传完成后，使用 http 302 的方式跳转到该 URL 
//为了插件正常工作，这里需要给出一个本域的url完成跳转获取参数，这个地址可以任意，只要是跟网站同域的。
$options['return-url'] =$base_url.'/no-use.txt';

// 异步回调 url：表单上传完成后，云存储服务端主动把上传结果 POST 到该 URL
// 请注意该地址必须公网可以正常访问
// $options['notify-url'] = 'http://www.demobucket.com/notify.php'; 

// 计算 policy 内容，具体说明请参阅"Policy 内容详解"
$policy = base64_encode(json_encode($options));

// 计算签名值，具体说明请参阅"Signature 签名"
$signature = md5($policy.'&'.$form_api_secret);

?>
<html>
<head>
<title>ajax file upload upyun For Jquery</title>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/upload-upyun.js"></script>
<script type="text/javascript">
	var policy = "<?php echo $policy ?>"; 
	var signature = "<?php echo $signature  ?>";
    var bucket = "<?php echo $bucket ?>"; 
	function upload() {
	
		$(document).ajaxStart(function(){
			$("#loading").show();
		});
		$(document).ajaxComplete(function(){
			$("#loading").hide();
		});
	
	
		$.upload2upyun({
			bucket:bucket,
			fileSelector:'#uploadFile',
			policy:policy,
			signature:signature,
			success:function(url,time,sign){
				  console.log(arguments);
				  $('#time').text(time);
				  $('#sign').text(sign);
				  var fullUrl = "http://"+bucket+".b0.upaiyun.com"+url;
				  $('#url').attr('href',fullUrl).text(fullUrl);
				  $('#result').show();
			},
			error:function(code,msg){
				alert(msg);
			}
		})
		return false;
	}
</script>
</head>
<body>
	<div id="wrapper">
		<div id="content">
			<h1>又拍云ajax上传插件</h1>
			<p>
				<a href="https://gitcafe.com/xujif/jquery-ajaxupload-for-UPYUN">项目主页</a>
			</p>
			<p>
				<h4>使用方法：</h4>
				<pre> 
                $.upload2upyun({
                    bucket:"your bucket",
                    fileSelector:'#uploadFile',
                    policy:policy,
                    signature:signature,
                    success:function(url,time,sign){
                        console.log(arguments) 
                    },
                    error:function(code,msg){
                        alert(msg);
                    }
                })
				</pre>
			</p>
			<h2>下面是demo</h2>
			<img id="loading" src="images/loading.gif" style="display: none;">
			<form id="form" name="form" action="" method="POST"
				enctype="multipart/form-data">
				<input id='uploadFile' type="file" name="file" /> <input
					type="button" value="提交" onclick="return upload()" />
			</form>
			<div id="result" style="display:none">
				<p>time:<span id="time"></span></p>
				<p>sign:<span id="sign"></sign></p>
				<p>url:<a id="url" target="_blank"></a></p>
			</div>
		</div>
</body>
</html>