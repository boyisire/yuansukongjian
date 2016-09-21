<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
/**
 * 后台我的资料--我的头像控制器
 * @使用表：kj_cms_account
 * @author lushijun
 * @time 2016-8-16
 */
class ManagerController extends CmsBaseController{
	/**
	 * 我的资料
	 */
	public function index(){
		// 获取当前用户信息
		$res=D('Cms_account')->getUserMessage($this->uid);
		$this->assign('res',$res['data']);
		$this->display();
	}
	/**
	 * 修改个人资料操作
	 */
	public function updateMessage(){
		if(IS_POST){
			$data=array(
				'nick_name'=>I('nick_name'),
				'user_email'=>I('user_email'),
				'user_phone'=>I('user_phone')
			);
			$res=D('Cms_account')->updateUser($this->uid,$data);
			if($res['code'] =='200'){
				$this->success('个人资料修改成功','index');
			}else{
				$this->error($res['msg'],'index');
			}
		}else{
			$this->error('错误操作','index');
		}
	}
	/**
	 * 上传头像页面
	 */
	public function upload(){
		$this->display();
	}
	/**
	 * 上传头像操作
	 */
	public function uploads_header(){
		$res=D('Cms_account')->upload_header($this->uid);
		if($res['code'] =='200'){
			$this->success('头像上传成功','upload');
		}else{
			$this->error('头像上传失败','upload');
		}
	}
	/**
	 * 修改密码页面
	 */
	public function password(){
		$this->display();
	}
	/**
	 * 修改密码操作
	 */
	public function updatePassword(){
		if(IS_POST){
			$data=array(
					'password'=>I('password'),
					'new_password'=>I('new_password'),
					'res_password'=>I('res_password')
			);
			$res=D('Cms_account')->updatePassword($this->uid,$data);
			if($res['code'] =='200'){
				session('[destroy]');
				$this->success('登录密码修改成功,请重新登录','password');
			}else{
				$this->error($res['msg'],'password');
			}
		}else{
			$this->error('错误操作','password');
		}
	}
}
