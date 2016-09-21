<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 用户登录操作model
 * @使用表 cms_account
 */
class Cms_accountModel extends BaseModel{

	/**
	 * 检查用户提交数据
	 * @param   array           提交post的数据
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  lushijun
	 * @time    2016-8-12
	 */
	public function checkUser($data){
		if(empty($data)){
			return array('code'=>'201','msg'=>'用户名或密码不能为空');
		}
		if(empty($data['user_name']) || empty($data['password'])){
			return array('code'=>'202','msg'=>"用户名或密码不能为空");
		}
		if(empty($data['code'])){
			return array('code'=>'203','msg'=>'验证码不能为空');
		}
		if(empty($data['form_id']) || $data['form_id'] != $_SESSION['formId']){
			return array('code'=>'204','msg'=>"错误操作，请刷新页面重试");
		}
		if(check_verify($data['code']) == false){
			return array('code'=>'205','msg'=>'验证码错误，请重新输入');
		}
		//查询数据
		$res=$this->findData(array('user_name'=>$data['user_name']));
		if(empty($res)){
			return array('code'=>'206','msg'=>'账号或密码错误');
		}
		if($res['password'] != sha1($data['password'])){
			return array('code'=>'207','msg'=>'账号或密码错误');
		}
		//账号密码都没有问题，赋值到session,并返回200成功
		session('formId',null);
		session('s_uinfo.uid',$res['id']);
		session('s_uinfo.login_time',$res['login_time']);
		session('s_uinfo.login_ip',$res['login_ip']);
		session('s_uinfo.is_login',1);
		session('s_uinfo.user_power',$res['user_power']);
		session('s_uinfo.nick_name',$res['nick_name']);
		session('s_uinfo.user_header',$res['user_header']);
		//修改登录用户的时间—IP
		$this->editData(array('id'=>$res['id']), array('login_id'=>get_client_ip(),'login_time'=>time()));
		return array('code'=>'200','msg'=>'登录成功');
		
	}
	/**
	 * 修改用户资料
	 */
	public function updateUser($uid,$data){
		$res=$this->editData(array('id'=>$uid), $data);
		if($res){
			session('s_uinfo.nick_name',$data['nick_name']);
			return array('code'=>'200','msg'=>'成功');
		}else{
			return array('code'=>'201','msg'=>'修改个人资料失败');
		}
	}
	/**
	 * 修改密码操作
	 */
	public function updatePassword($uid,$data){
		//判断两次输入的密码
		if(empty($data['new_password']) || empty($data['res_password'])){
			return array('code'=>'204','msg'=>'新密码不能为空');
		}
		if($data['new_password'] != $data['res_password']){
			return array('code'=>'201','msg'=>'两次输入的密码不一样');
		}
		if(strlen($data['new_password']) < 6){
			return array('code'=>'202','msg'=>'新密码长度不能小于6位数字和字母组合');
		}
		//根据id获取当前用户信息
		$res=$this->findData(array('id'=>$uid), "password");
		if($res['password'] != sha1($data['password'])){
			return array('code'=>'203','msg'=>'原密码输入错误');
		}
		//修改用户密码
		$update=$this->editFieldData(array('id'=>$uid),'password',sha1($data['new_password']));
		if($update){
			return array('code'=>'200','成功');
		}else{
			return array('code'=>'205','msg'=>'修改失败');
		}
	}
	/**
	 * 用户头像上传
	 */
	public function upload_header($uid){
		//获取当前用户信息
		$res=$this->findData(array('id'=>$uid), "user_header");
		$path="./Upload/Cms/user_header/";
		$uploads=upload_image($path);
		if(!empty($uploads)){
			$data['user_header']=$uploads;
			$editHeader=$this->editFieldData(array('id'=>$uid),'user_header',$uploads[0]);
			if($editHeader){
				session('s_uinfo.user_header',$uploads[0]);
				return array('code'=>'200','msg'=>'成功');
			}else{
				return array('code'=>'201','msg'=>"修改数据失败");
			}
		}else{
			return array('code'=>'202','上传失败');
		}
	}
	/**
	 * 获取当前用户的信息
	 */
	public function getUserMessage($user_id){
		if($user_id){
			$res=$this->findData(array('id'=>$user_id));
			return array('code'=>'200','msg'=>'成功','data'=>$res);
		}else{
			return array('code'=>'201','msg'=>'查询失败');
		}
	}
	/**
	 * 获取所有管理员信息
	 */
	public function getAllMemberList($uid,$user_power){
		//判断当前用户权限，是不是超级管理员，
		if($user_power =='100'){
			$res=$this->selectPageData();
			if(!empty($res['list'])){
				foreach($res['list'] as $key=>$val){
					if(empty($val['user_header'])){
						$res['list'][$key]['user_header']="/Upload/Cms/user_header/profile_small.jpg";
					}
					if($val['user_power'] =='100'){
						$res['list'][$key]['power_name']="超级管理员";
					}else{
						$res['list'][$key]['power_name']="管理员";
					}
					$res['list'][$key]['add_time']=date('Y-m-d H:i:s',$val['add_time']);
					$res['list'][$key]['login_time']=date('Y-m-d H:i:s',$val['login_time']);
				}
			}
			return $res;
		}else{
			return array('code'=>'201','msg'=>'对不起，你没有权限查看管理员信息');
		}
	}
	/**
	 * 修改管理员的资料
	 */
	public function updateMemberAccount($user_power,$id,$data){
		if($user_power !='100'){
			return array('code'=>'201','msg'=>'对不起您没有权限进行此操作');
		}
		if(empty($data['user_name'])){
			return array('code'=>'204','msg'=>'登录账户不能为空');
		}
		if(empty($data['user_email']) || empty($data['user_phone'])){
			return array('code'=>'205','msg'=>'邮箱或手机号码不能为空');
		}
		$path="./Upload/Cms/user_header/";
		$uploads=upload_image($path);
		if(!empty($uploads)){
			$data['user_header']=$uploads[0];
		}
		//提交修改
		$result=$this->editData(array('id'=>$id),$data);
		if($result){
			return array('code'=>'200','msg'=>'成功');
		}else {
			return array('code'=>'206','msg'=>'修改失败');
		}
	}
	/**
	 * 添加管理员操作
	 */
	public function addMemberAccount($user_id,$user_power,$data){
		if($user_power !='100'){
			return array('code'=>'201','msg'=>'对不起您没有权限进行此操作');
		}
		if(empty($data['user_name'])){
			return array('code'=>'202','msg'=>'管理员账号不能为空');
		}
		if(empty($data['user_email']) || empty($data['user_phone'])){
			return array('code'=>'203','msg'=>'邮箱或手机号不能为空');
		}
		if(empty($data['nick_name'])){
			return array('code'=>'204','msg'=>'管理员昵称不能为空');
		}
		if(empty($data['user_power'])){
			$data['user_power']="50";
		}
		//判断当前账号是否存在
		$account=$this->findData(array('user_name'=>$data['user_name'],'id'));
		if($account){
			return array('code'=>'205','msg'=>'该账号已存在，请使用其他账号');
		}
		$path="./Upload/Cms/user_header/";
		$uploads=upload_image($path);
		if(!empty($uploads)){
			$data['user_header']=$uploads[0];
		}
		$data['add_time']=time();
		$data['login_time']=time();
		$data['login_ip']=get_client_ip();
		$result=$this->addData($data);
		if($result){
			return array('code'=>'200','msg'=>'成功');
		}else {
			return array('code'=>'206','msg'=>'添加管理员失败');
		}
	}
	/**
	 * 删除管理员操作
	 */
	public function delMemberAccount($id,$user_power){
		if($user_power !='100'){
			return array('code'=>'201','msg'=>'对不起您没有权限进行此操作');
		}
		if(empty($id)){
			return array('code'=>'202','msg'=>'删除失败，错误的操作');
		}
		$result=$this->deleteData(array('id'=>$id));
		if($result){
			return array('code'=>'200','msg'=>'成功');
		}else{
			return array('code'=>'203','msg'=>'管理员删除失败');
		}
	}
}
