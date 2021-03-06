<?php
/*************************************************
 * 亚杰活动页面
 * @使用表 kj_activity,kj_activity_info,kj_activity_info_user,kj_activity_votes
 * @date:               2016-8-31
 * @author:             lushijun
 * @version:            v1.0
 *************************************************/
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Common\Model\UsersModel;
class ActivityController extends HomeBaseController{
	/**
	 * 活动说明页面
	 */
	public function actIndex(){
		//获取当前cookie名字
		$data['uid']=I('cu');
		$data['utype']=I('ct');
		$cookieName=cookie('yjname');
		if(empty($cookieName) || !isset($_COOKIE['yjname'])){
			//如果不存在，则插入库里记录当前用户
			$res=D('Activity_user_count')->insertUserData(array('time_status'=>'1'),$data);
		}else{
			//否则则查询数据库判断是否存在
			$result=D('Activity_user_count')->findData(array('cookie_name'=>$cookieName),'id');
			if(empty($result)){
				$res=D('Activity_user_count')->insertUserData(array('time_status'=>'1'),$data);
			}
		}
		$url="http://kongjian.yuansuzhouqi.cn";
		$signPackage=wx_api_share();
		$this->assign('url',$url);
		$this->assign('signPackage',$signPackage);
		$this->display();
	} 
	
	/**
	 * ajax 获取用户的第二步操作
	 */
	public function corpsCount(){
		$cookieName=cookie('yjname');
		$status=I('status');
		$phone=I('corps_phone');
		if(empty($phone)){
			$data=array('time_status'=>$status);
		}else{
			$data=array('time_status'=>$status,'phone'=>$phone);
		}
		if(empty($cookieName) || !isset($_COOKIE['yjname'])){
			//如果不存在，则插入库里记录当前用户
			$res=D('Activity_user_count')->insertUserData($data);
		}else{
			//否则则查询数据库判断是否存在
			$result=D('Activity_user_count')->findData(array('cookie_name'=>$cookieName),'id,time_status');
			if(empty($result)){
				$res=D('Activity_user_count')->insertUserData($data);
			}elseif(!empty($result) && $result['time_status'] != $status){
				$res=D('Activity_user_count')->editData(array('id'=>$result['id']),$data);
			}else{
				$res=$result['id'];
			}
		}
		if($res){
			$this->ajaxReturn(array('result'=>'2','data'=>'true'));
		}else{
			$this->ajaxReturn(array('result'=>'1','data'=>'false'));
		}
	}
	/**
	 * 亚杰显示投票页面
	 */
	public function index(){
		//获取所有战队信息
		$result=D('Activity_info')->selectActivityData(array('is_show'=>'1'));
		$votes=array();
		foreach($result as $val){
			$votes[]=$val['corps_votes'];
			$corps_name[]="'NO.".$val['is_sort'].'-'.$val['corps_name']."'";
		}
		$this->assign('votes',implode(',',$votes));
		$this->assign('corps_name',implode(',',$corps_name));
		$this->display();
	}
	/**
	 * ajax 刷新获取最新票数
	 */
	public function ajaxIndex(){
		$result=D('Activity_info')->selectActivityData(array('is_show'=>'1'),'corps_votes');
		$votes=array();
		foreach($result as $val){
			$votes[]=$val['corps_votes'];
		}
		//$votes=implode(',',$votes);
		$this->ajaxReturn($votes);
	}
	/**
	 * 亚杰活动主题页面
	 */
	public function indexTheme(){
		if ($this->isMobile()){
			$url="http://kongjian.yuansuzhouqi.cn";
			$signPackage=wx_api_share();
			$this->assign('url',$url);
			$this->assign('signPackage',$signPackage);
			$this->display('Activity/yajiemobile');
		}else{
			$this->display('Activity/yajiepc');
		}
	}
	/**
	 * 判断是不是手机访问
	 */
	private function isMobile(){
		$useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
		
		$mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
		$mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');
		$found_mobile=$this->CheckSubstrs($mobile_os_list,$useragent_commentsblock) || $this->CheckSubstrs($mobile_token_list,$useragent);
		if ($found_mobile){
			return true;
		}else{
			return false;
		}
	
	}
	/**
	 * 判断是不是手机访问
	 */
	private function CheckSubstrs($substrs,$text){
		foreach($substrs as $substr)
			if(false!==strpos($text,$substr)){
			return true;
		}
		return false;
	
	}
    /**
     * 亚杰投票页面
     */
	public function votes(){
		//活动id
		$cid="1";
		//获取所有战队信息
		$result=D('Activity_info')->selectActivityData(array('is_show'=>'1'));
		//获取当前战队的成员
		if(!empty($result)){
			foreach($result as $key=>$val){
				$result[$key]['child']=D('Activity_info_user')->selctCorpsMessage(array('is_show'=>'1','cid'=>$val['id']));
			}
		}
		//获取当前登录用户信息，判断用户是否存在手机
		$User = new UsersModel('users','wp_','connection');
		$userResult=$User->findUserFromId(array('ID'=>$this->uid));
		//获取当前活动开始--结束时间
		$act=D('Activity')->findData(array('id'=>$cid),'');
		$time=time();
		//判断当前活动是否结束
		if($time > $act['end_time2']){
			$this->redirect('Home/Activity/over');
		}
		//判断当前用户有没有投过票,根据当前时间判断投票处于哪个阶段
		
		//判断是那种模式的投票时间
		if($act['is_time'] =='2'){
			if($time >= $act['start_time'] && $time < $act['end_time']){
				//组合条件判断用户是否有投过票
				$where['user_id']=array('eq',$this->uid);
				$where['cid']=array('eq',$cid);
				$where['add_time']=array(array('EGT',$act['start_time']),array('lt',$act['end_time']), 'and');
				$votes=M('activity_votes')->where($where)->find();
			}else{
				if($time >= $act['start_time2'] && $time < $act['end_time2']){
					//组合条件判断用户是否有投过票
					$where['user_id']=array('eq',$this->uid);
					$where['cid']=array('eq',$cid);
					$where['add_time']=array(array('EGT',$act['start_time2']),array('lt',$act['end_time2']), 'and');
					$votes=M('activity_votes')->where($where)->find();
				}else{
					$votes="不在投票时间段内";
				}
			}
		}else{
			if($time >= $act['start_time'] && $time < $act['end_time']){
				//组合条件判断用户是否有投过票
				$where['user_id']=array('eq',$this->uid);
				$where['cid']=array('eq',$cid);
				$where['add_time']=array('EGT',$act['start_time']);
				$where['add_time']=array('LT',$act['end_time']);
				$votes=D('Activity_votes')->findUserVotesData($where,'*');
			}else{
				$votes="不在投票时间段内";
			}
		}
		if(empty($votes)){
			//未投过
			$user_votes="1";
		}else{
			//已投过
			$user_votes="2";
		}
		$url="http://kongjian.yuansuzhouqi.cn";
		$signPackage=wx_api_share();
		$this->assign('url',$url);
		$this->assign('signPackage',$signPackage);
		$this->assign('cid',$cid);
		$this->assign('userResult',$userResult);
		$this->assign('user_votes',$user_votes);
		$this->assign('result',$result);
		$this->display();
	}
	/**
	 * 发送邮件
	 */
	public function corpsD(){
		$User = new UsersModel('users','wp_','connection');
		$userResult=$User->findData(array('ID'=>'5'),'user_mobile','');
		if(empty($userResult['user_mobile'])){
			$userResult['user_mobile']=$_POST['phone'];
		}
		if(empty($_SESSION['send_error'])){
			$_SESSION['send_error']="1";
		}else{
			$_SESSION['send_error']=$_SESSION['send_error']+1;
		}
		if($_SESSION['send_error'] > 30){
			$reason="用户操作失败";
			$value = array($userResult['user_mobile'],$reason);
			$templ = C('SMS_TEMPLATE')['CYZ_Apply_Erro'];
			$send=sendSMS('13522991311',$value,$templ);
		}
		$this->ajaxReturn(array('result'=>'1','data'=>'发送成功'));

	}
	/**
	 * 直接发送短信操作
	 */
	public function corpsA(){
		if(IS_POST){
			$User = new UsersModel('users','wp_','connection');
			$userResult=$User->findData(array('ID'=>$this->uid),'user_mobile','');
			if(empty($userResult['user_mobile'])){
				$this->ajaxReturn(array('result'=>'3','data'=>'没找到手机号码'));
			}
			$data=array('phone'=>$userResult['user_mobile'],'cid'=>I('cid'),'user_id'=>$this->uid);
			$result=D('Sms_info')->yaJieSendSms($data);
			if($result['result'] =='2'){
				$data['code']=$result['data'];
				//插入到短信表信息
				$sms=D('Sms_info')->insertSmsInfo($data);
				if($sms['result'] =='2'){
						$sms['result']="2";
						$sms['data']="验证码已送至手机:".$userResult['user_mobile'];
						$sms['msg']=$userResult['user_mobile'];
				}else{
					$sms['result']="4";
				}
			}else{
				$sms['result']="4";
				 $sms['data']="验证码发送失败";
			}

			$this->ajaxReturn($sms);
		}else{
			$this->ajaxReturn(array('result'=>'1','data'=>'错误操作'));
		}
	}
	/**
	 * 接受手机号发送短信
	 */
	public function corpsS(){
		if(IS_POST){
			$data=array('phone'=>I('corps_phone'),'cid'=>I('cid'),'user_id'=>$this->uid);
			//判断当前手机号码是否已使用
			//绑定手机号
			$User = new UsersModel('users','wp_','connection');
			$user_phone=$User->findData(array('user_mobile'=>$data['phone']),"id");
			if(empty($user_phone) || empty($user_phone['id'])){
				//插入到短信表信息
				$result=D('Sms_info')->yaJieSendSms($data);
				if($result['result'] =='2'){
					$data['code']=$result['data'];
					$sms=D('Sms_info')->insertSmsInfo($data);
					if($sms['result'] =='2'){
							$sms['result']="2";
							$sms['data']="验证码已送至手机:".$data['phone'];
							$sms['msg']=$data['phone'];
					}else{
						$sms['result']="1";
						$sms['data']="验证码发送失败";
					}
				}else{
					$sms['result']="1";
					$sms['data']="验证码发送失败";
				}
			}else{
				$sms['result']="1";
				$sms['data']="手机号码已存在";
			}
			$this->ajaxReturn($sms);
		}else{
			$this->ajaxReturn(array('result'=>'1','data'=>'错误操作'));
		}
	}
	/**
	 * 验证短信验证码，并投票
	 */
	public function corpsC(){
		if(IS_POST){
	$data=array('phone'=>I('corps_phone'),'cid'=>I('cid'),'user_id'=>$this->uid,'corps_code'=>I('corps_code'),'id'=>I('id'),'corps_user'=>I('corps_user'));
	//获取短信信息
			$sms=D('Sms_info')->findData(array('accept_user'=>$data['user_id'],'sms_type'=>'4','sms_status'=>'1'),'id,code,end_time,send_time,is_use','id desc');
			if(empty($sms)){
				$this->ajaxReturn(array('result'=>'1','data'=>'验证码错误'));
			}else{
				if((int)$sms['code'] == (int)$data['corps_code']){
					$time=time();
					if($time > $sms['send_time'] && $time < $sms['end_time']){
						if($sms['is_use'] =='1'){
							//验证码ok，开始投票
							$result=D('Activity_votes')->insertUserVotes($data);
							if($result['result'] =='2'){
								//插入投票数据成功，更改战队票数
								//获取当前战队票数
								$votes=D('Activity_info')->findActivityInfoData(array('id'=>$data['id']),'corps_votes');
								$now_votest=$votes['corps_votes']+1;
								//更改票数
								$user_votes=D('Activity_info')->editData(array('id'=>$data['id']),array('corps_votes'=>$now_votest));
								if($user_votes){
									if($data['corps_user'] =='2'){
										//绑定手机号
										$User = new UsersModel('users','wp_','connection');
										$user_phone=$User->editData(array('id'=>$data['user_id']), array('user_mobile'=>$data['phone']));
										//投票成功，修改短信状态
										$userSms=D('Sms_info')->editData(array('id'=>$sms['id']),array('is_use'=>'2'));
									}
									$this->ajaxReturn(array('result'=>'2','data'=>'投票成功'));
								}else{
									$this->ajaxReturn(array('result'=>'1','data'=>'投票失败'));
								}
							}else{
								$this->ajaxReturn($result);
							}
						}else{
							$this->ajaxReturn(array('result'=>'1','data'=>'验证码已使用，请从新发送'));
						}
					}else{
						$this->ajaxReturn(array('result'=>'1','data'=>'验证码已过期'));
					}
				}else{
					$this->ajaxReturn(array('result'=>'1','data'=>'验证码错误'));
				}
			}
		}else{
			$this->ajaxReturn(array('result'=>'1','data'=>'错误操作'));
		}
	}
	/**
	 * 投票成功页面
	 */
	public function view(){
		
		$this->display();
	}
	/**
	 * 投票结束页面
	 */
	public function over(){
		$this->display();
	}
}

