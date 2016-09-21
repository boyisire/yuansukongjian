/**
 * Created by tiandiguoshi on 2016/9/12.
 */
//判断企业信息提交
function ckform(){
	alert('1111111111');
    var company_name = $("#company_name").val();
	alert(company_name);
    var content = $("#content").val();
    var user_name = $("#user_name").val();
	var user_phone = $("#user_phone").val();
	var upfile=$("#upfile").val();
    var arr = {company_name:company_name,content:content,user_name:user_name,user_phone:user_phone,upfile:upfile};
    var sign = '';
    $.each(arr, function() {
        if(arr['company_name'] == ''){
            $("#error_company_name").html("公司地址不能为空！");
            sign = 1;
        }

        if(arr['content'] == ''){
            $("#error_content").html("产品或服务描述不能为空！");
            sign = 1;
        }else{
        	if(arr['content'].length < 100){
			 $("#error_content").html("产品或服务描述不能少于100字！");
            sign = 1;
			}
        }
        if(arr['user_name'] == ''){
            $("#error_user_name").html("负责人不能为空！");
            sign = 1;
        }
		if(arr['user_phone'] ==''){
			$("#error_user_phome").html('手机号码不能为空！');
		}else{
			if(isphone2(arr['user_phone'])){
	        }else{
		       $("#error_user_phome").html('手机号码格式不正确！');
	            sign = 1;
	        }
		
		}
		if(arr['upfile'] ==''){
       	    $("#error_upfile").html('请上传你的商业计划书');
            sign = 1;
        }else{
        	if(!(/(?:pdf|ppt|docx)$/i.test(arr['upfile']))) {      		
        		 $("#error_upfile").html('您上传的文件不符合要求，请重新上传');
            	 sign = 1;
        	}
		}
    });
	alert(sign);
    if(sign == 1){ return false;}
}
function isphone2(inputString){
     var partten = /^1[34578][0-9]{9}$/;
     var fl=false;
     if(partten.test(inputString)){
          return true;
     }else{
    	 var parttens = /^([0-9]{3,4}-)?[0-9]{7,8}$/;
    	 if(parttens.test(inputString)){
              return true;
         }else{
              return false;
         }
     }
}