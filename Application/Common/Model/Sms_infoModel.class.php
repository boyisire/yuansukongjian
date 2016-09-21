<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 短信表model
 * @使用表 sms_info
 */
class Sms_infoModel extends BaseModel{
	/**
	 * 获取所有的短信信息
	 */
	public function sendSmsSelect($data){
		if(!empty($data['like'])){
			$where['sms_desc']=array('like',"%".$data['like']."%");
		}
		if(!empty($data['sms_type'])){
			$where['sms_type']=array('eq',$data['sms_type']);
		}
		$result=$this->selectPageData('*',$where,'',16);
		if($result['list']){
			foreach($result['list'] as $key=>$val){
				if($val['sms_type'] =='1'){
					$result['list'][$key]['sms_name']="验证码";
					$result['list'][$key]['background']="background-color:#1ab394";
				}elseif($val['sms_type'] =='2'){
					$result['list'][$key]['sms_name']="系统短信";
					$result['list'][$key]['background']="background-color:#EF5352";
				}else{
					$result['list'][$key]['sms_name']="审核短信";
					$result['list'][$key]['background']="background-color:#337ab7";
				}
				if($val['sms_status'] =='1'){
					$result['list'][$key]['sms_status']="<font color='green'>发送成功</font>";
				}else{
					$result['list'][$key]['sms_status']="<font color='red'>发送失败</font>";
				}
			}
		}
		return $result;
	}
	/**
	 * 亚杰发送短信验证码操作
	 */
	public function yaJieSendSms($data){
		$result=sendVerificationCode($data['phone']);
		if($result['code'] =='200'){
			return array('result'=>'2','data'=>$result['obj']);
		}else{
			return array('result'=>'1','data'=>$result['msg']);
		}
	}
	/**
	 * 亚杰活动插入短信内容
	 */
	public function insertSmsInfo($data){
		if(empty($data)){
			return array('result'=>'1','data'=>'错误操作!');
		}
		if(empty($data['phone'])){
			return array('result'=>'1','data'=>'手机号码不能为空!');
		}
		if(empty($data['user_id'])){
			return array('result'=>'1','data'=>'您还没有登录!');
		}
		if(!preg_match('/^(((13[0-9]{1})|(14[0-9]{1})|(17[0-7]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/',$data['phone'])){
			//验证失败
			return array('result'=>'1','data'=>'手机格式不正确!');
		}
		$code=$data['code'];
		$content="验证码：".$code."  感谢您参与亚杰国际创业音乐节。元素周期是第一家专注于传媒孵化的创业服务平台http://yuansuzhouqi.cn ";
		$arr=array(
				'sms_type'=>'4',
				'code'=>$code,
				'sms_status'=>'1',
				'accept_user'=>$data['user_id'],
				'sms_desc'=>$content,
				'send_time'=>time(),
				'is_use'=>'1',
				'end_time'=>time()+600
		);
		$result=$this->addData($arr);
		if($result){
			return array('result'=>'2','data'=>$code,'sms_id'=>$result);
		}else{
			return array('result'=>'1','data'=>'验证码发送失败');
		}
	}
	/**
	 * 发送短信操作
	 */
	public function sendUserSms($data){
		if(empty($data)){
			return "";
		}
		foreach($data as $key=>$val){
			
		}
	}
}
