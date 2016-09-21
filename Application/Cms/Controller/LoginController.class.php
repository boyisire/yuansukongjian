<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
/**
 * 后台登录页面控制器
 * @使用表：kj_cms_account
 * @author lushijun
 * @time 2016-8-14
 */
class LoginController extends CmsBaseController{
	/**
	 * 首页
	 */
	public function index(){
		//元素空间后台登录密码yskj2016!@#$%^，账号yskj2016
		$this->assign('form',$this->getRandomString(6));
		$this->display();
	}
	/**
	 * 用户登录操作
	 */
	public function loginIn(){
		if(IS_POST){
			$arr=array(
				'form_id'=>I('fn'),
				'user_name'=>I('user_name'),
				'password'=>I('password'),
				'code'=>$_POST['code']
			);
			$result=D('Cms_account')->checkUser($arr);
			if($result['code'] =='200'){
				$this->redirect('Cms/index/index');
			}else{
				$this->error($result['msg'],'index');
			}
		}else{
			$this->error('错误操作','index');
		}

	}

	/**
	 * 验证码
	 */
	public function code(){
		show_verify();
	}
}
