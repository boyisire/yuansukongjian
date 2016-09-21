<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 用户信息表model
 * @使用表 wp-users
 * @author lushijun
 * @time 2016-8-19
 */
class UsersModel extends BaseModel{
	/**
	 * 获取所有用户的所有信息
	 */
	public function selectUserData($data){
		if(empty($data)){
			return "没找到用户";
		}
		$result=$this->findData($data,"*");
		return $result;
	}
	/**
	 * 获取用户所有信息
	 */
	public function findUser($data){
		if(empty($data)){
			return "没找到用户";
		}
		$result=$this->findData($data,"user_nicename");
		return $result['user_nicename'];
	}
	/**
	 * 获取用户的手机号码
	 */
	public function findUserPhone($data){
		if(empty($data)){
			return "";
		}
		$data=explode('|', $data);
		//return $data;
		foreach($data as $val){
			$user_mobile=$this->findData(array('ID'=>$val),"user_mobile");
			$arr[]=$user_mobile['user_mobile'];
		}
		//$arr=implode(',', $arr);
		return $arr;
	}
	/**
	 * 发送短信操作
	 */
	public function sendUserSms($data){
		
	}
	/**
	 * $data array 用户id
	 * 根据id获取用户信息，并分页
	 */
	public function findUserFromId($data){
		if(empty($data)){
			return $data;
		}
		$arr=array();
		foreach($data as $val){
			$arr[]=$this->findData(array('ID'=>$val),"ID,user_mobile,user_nicename,user_email,user_registered");
		}
		return $arr;
	}
}
