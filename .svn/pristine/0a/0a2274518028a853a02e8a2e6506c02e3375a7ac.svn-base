<?php
namespace Common\Controller;
use Common\Controller\BaseController;
/**
 * cms 基类控制器
 */
class CmsBaseController extends BaseController{
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize();
		//判断当前访问的控制器，
		//如果action==login则不需要判断登录
		if(in_array(CONTROLLER_NAME,array('Login','login'))){
			//不需求登录
			$this->isLogin(0);
		}else{
			//需要登录
			$this->isLogin(1);
		}
		if($this->uid){
			if($this->user_power =='100'){
				$this->assign('power_name','超级管理员');
			}else{
				$this->assign('power_name','管理员');
			}
			$this->assign('uid',$this->uid);
			$this->assign('user_header',$this->user_header);
			$this->assign('nick_name',$this->nick_name);
			$this->assign('user_power',$this->user_power);
		}
	}
	//判断用户是否登录
	private function isLogin($perm){
		if($perm){
			if(isset($_SESSION['s_uinfo']) && !empty($_SESSION['s_uinfo']['uid']) && $_SESSION['s_uinfo']['is_login'] == '1'){
				$this->uid=$_SESSION['s_uinfo']['uid'];
				$this->is_login=$_SESSION['s_uinfo']['is_login'];
				$this->login_time=$_SESSION['s_uinfo']['login_time'];
				$this->login_ip=$_SESSION['s_uinfo']['login_ip'];
				$this->user_power=$_SESSION['s_uinfo']['user_power'];
				if(empty($_SESSION['s_uinfo']['user_header'])){
					$this->user_header="/Upload/Cms/user_header/profile_small.jpg";
				}else{
					$this->user_header=$_SESSION['s_uinfo']['user_header'];
				}		
				$this->nick_name=$_SESSION['s_uinfo']['nick_name'];
				return true;
			}else{
				$this->redirect('Cms/Login/index');
			}
		}else{
			if(isset($_SESSION['s_uinfo']) && !empty($_SESSION['s_uinfo']['uid']) && $_SESSION['s_uinfo']['is_login'] == '1'){
					
				$this->redirect('Cms/Index/index');
			}else{
				return true;
			}
		}
	}
	public function getRandomString($len, $chars=null){
		if (is_null($chars)){
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		}
		mt_srand(10000000*(double)microtime());
		for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
			$str .= $chars[mt_rand(0, $lc)];
		}
		session('formId',$str);
		return $str;
	}



}

