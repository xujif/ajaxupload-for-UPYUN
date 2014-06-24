<?php
header("Content-type: text/html; charset=utf-8"); 
$base_url= 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER["PHP_SELF"]);
// �ռ���
$bucket = 'testxujif';

// �� API ��֤�ܳף���Ҫ���������ƹ����̨�Ŀռ����ҳ���ȡ
$form_api_secret = 'MpFNdOcm2J0AieiGkB5GsW+7B4g=';
$options = array();
$options['bucket'] = $bucket;
// ��Ȩ����ʱ�䣺��ҳ�������Ͽ�ʼ��ʱ��10��������Ч
$options['expiration'] = time()+600;
// ����·�������ս���"/��/��/��/upload_���ϴ��ļ���"����ʽ���б���
$options['save-key'] = '/{year}/{mon}/{day}/upload_{filename}{.suffix}';

// ͬ����ת url�����ϴ���ɺ�ʹ�� http 302 �ķ�ʽ��ת���� URL 
//Ϊ�˲������������������Ҫ����һ�������url�����ת��ȡ�����������ַ�������⣬ֻҪ�Ǹ���վͬ��ġ�
$options['return-url'] =$base_url.'/no-use.txt';

// �첽�ص� url�����ϴ���ɺ��ƴ洢������������ϴ���� POST ���� URL
// ��ע��õ�ַ���빫��������������
// $options['notify-url'] = 'http://www.demobucket.com/notify.php'; 

// ���� policy ���ݣ�����˵�������"Policy �������"
$policy = base64_encode(json_encode($options));

// ����ǩ��ֵ������˵�������"Signature ǩ��"
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
			<h1>������ajax�ϴ����</h1>
			<p>
				<a href="https://gitcafe.com/xujif/jquery-ajaxupload-for-UPYUN">��Ŀ��ҳ</a>
			</p>
			<p>
				<h4>ʹ�÷�����</h4>
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
			<h2>������demo</h2>
			<img id="loading" src="images/loading.gif" style="display: none;">
			<form id="form" name="form" action="" method="POST"
				enctype="multipart/form-data">
				<input id='uploadFile' type="file" name="file" /> <input
					type="button" value="�ύ" onclick="return upload()" />
			</form>
			<div id="result" style="display:none">
				<p>time:<span id="time"></span></p>
				<p>sign:<span id="sign"></sign></p>
				<p>url:<a id="url" target="_blank"></a></p>
			</div>
		</div>
</body>
</html>