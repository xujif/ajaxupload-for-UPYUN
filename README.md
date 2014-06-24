#ajaxupload-for-UPYUN
又拍云ajax上传插件，基于jquery 和[表单api开发](http://wiki.upyun.com/index.php?title=%E8%A1%A8%E5%8D%95API%E6%8E%A5%E5%8F%A3
[查看demo](http://1.demo1234.sinaapp.com/ajaxupload/)

因为比赛在gitcafe.com发布https://gitcafe.com/xujif/ajaxupload-for-UPYUN
###使用：
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
		
###说明
为了安全考虑，policy和signature由后端生成。
注意的是，return-url需要指定为当前网站的一个地址（因为js同源策略）
示例代码用php为参考

    // 空间名
    $bucket = 'your bucket';
    // 表单 API 验证密匙：需要访问又拍云管理后台的空间管理页面获取
    $form_api_secret = 'form_api_secret';
    $options = array();
    $options['bucket'] = $bucket;
    // 授权过期时间：以页面加载完毕开始计时，10分钟内有效
    $options['expiration'] = time()+600;
    // 保存路径：最终将以"/年/月/日/upload_待上传文件名"的形式进行保存
    $options['save-key'] = '/{year}/{mon}/{day}/upload_{filename}{.suffix}';
    // 同步跳转 url：表单上传完成后，使用 http 302 的方式跳转到该 URL 
    //为了插件正常工作，这里需要给出一个本域的url完成跳转获取参数，这个地址可以任意，只要是跟网站同域的。
    $options['return-url'] = 'http://localhost/no-use.txt';

    // 异步回调 url：表单上传完成后，云存储服务端主动把上传结果 POST 到该 URL
    // 请注意该地址必须公网可以正常访问
    // $options['notify-url'] = 'http://www.demobucket.com/notify.php'; 
    
    // 计算 policy 内容，具体说明请参阅"Policy 内容详解"
    $policy = base64_encode(json_encode($options));
    
    // 计算签名值，具体说明请参阅"Signature 签名"
    $signature = md5($policy.'&'.$form_api_secret);
