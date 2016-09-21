<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
/**
 * 网站后台-所有管理员信息
 * @使用表-kj-cms_account
 * @author lushijun
 * @time 2016-8-17
 */
class MemberController extends CmsBaseController{
	/**
	 * 首页
	 */
	public function index(){
		//获取所有管理员信息
		$list=D('Cms_account')->getAllMemberList($this->uid,$this->user_power);
		$this->assign('list',$list);
		$this->display();
	}
	/**
	 * 管理员添加
	 */
	public function add(){
		$this->display();
	}
	/**
	 * 管理员添加操作
	 */
	public function addMember(){
		if(IS_POST){
			$new_password=I('new_password');
			$res_password=I('res_password');
			if(empty($new_password)){
				$this->error('管理员登录密码不能为空','addd');
			}
			if($new_password != $res_password){
				$this->error('两次输入的密码不一样','add');
			}
			$data=array(
					'user_name'=>I('user_name'),
					'password'=>sha1($new_password),
					'user_email'=>I('user_email'),
					'user_phone'=>I('user_phone'),
					'user_power'=>I('user_power'),
					'nick_name'=>I('nick_name')
			);
			$res=D('Cms_account')->addMemberAccount($this->uid,$this->user_power,$data);
			if($res['code'] =='200'){
				$this->success('管理员添加成功','index');
			}else{
				$this->error($res['msg'],'add');
			}
		}else{
			$this->error('错误操作','add');
		}
	}
	/**
	 * 修改管理员信息
	 */
	public function edit(){
		$id=I('id');
		$list=D('Cms_account')->getUserMessage($id);
		$this->assign('list',$list['data']);
		$this->display('Member/edit');
	}
	/**
	 * 修改管理员资料
	 */
	public function updateMember(){
		$id=I('id');
		if(IS_POST){
			$new_password=I('new_password');
			$res_password=I('res_password');
			if(empty(I('new_password'))){
				$data=array(
						'user_header'=>I('old_user_header'),
						'user_name'=>I('user_name'),
						'nick_name'=>I('nick_name'),
						'user_email'=>I('user_email'),
						'user_phone'=>I('user_phone'),
						'user_power'=>I('user_power')
				);
			}else{
				if(strlen($new_password) < 6){
					$this->error('新密码长度不能少于6位数字和字母');
				}
				if($new_password != $res_password){
					$this->error('两次输入的密码不一样');
				}
				$data=array(
						'user_header'=>I('old_user_header'),
						'password'=>sha1($new_password),
						'user_name'=>I('user_name'),
						'nick_name'=>I('nick_name'),
						'user_email'=>I('user_email'),
						'user_phone'=>I('user_phone'),
						'user_power'=>I('user_power')
				);
			}
			
			$res=D('Cms_account')->updateMemberAccount($this->user_power,$id,$data);
			if($res['code'] =='200'){
				$this->success('修改成功','edit?id='.$id);
			}else{
				$this->error($res['msg'],'edit?id='.$id);
			}
		}else{
			$this->error('错误操作','edit?id='.$id);
		}
	}
	/**
	 * 删除管理员
	 */
	public function del(){
		$id=I('id');
		$res=D('Cms_account')->delMemberAccount($id,$this->user_power);
		if($res['code'] =='200'){
			$this->success('删除成功','index');
		}else{
			$this->error($res['msg'],'index');
		}
	}
}
