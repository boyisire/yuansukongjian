<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 战队成员表model
 * @使用表 kj_activity_info_user
 * @author lushijun
 * @time 2016-8-30
 */
class Activity_info_userModel extends BaseModel{
	/**
	 * 添加成员信息
	 */
	public function inAddMember($data){
		if(empty($data['user_name'])){
			return array('code'=>'201','msg'=>'战队成员名称不能为空');
		}
		if(empty($data['cid'])){
			return array('code'=>'202','msg'=>'战队id不能为空');
		}
		if(empty($data['user_dec'])){
			return array('code'=>'203','msg'=>'战队成员说明不能为空');
		}
		$result=$this->addData($data);
		if($result){
			return array('code'=>'200','成功');
		}else{
			return array('code'=>'204','战队成员添加失败');
		}
	}
	/**
	 * 获取当前战队下的所有成员信息
	 */
	public function selctCorpsMessage($data){
		$result=$this->selectData('id,user_name,user_dec,is_show',$data,'is_sort asc');
		return $result;
	}
	/**
	 * 修改战队成功信息
	 */
	public function inUpdateMember($data,$where){
		if(empty($data['user_name'])){
			return array('code'=>'201','msg'=>'战队成员名称不能为空');
		}
		if(empty($data['user_dec'])){
			return array('code'=>'203','msg'=>'战队成员说明不能为空');
		}
		$result=$this->editData($where, $data);
		if($result){
			return array('code'=>'200','成功');
		}else{
			return array('code'=>'204','修改战队成员信息失败');
		}
	}
	/**
	 * 删除战队下成员信息
	 */
	public function delMemberCorps($where){
		return $this->deleteData($where);
	}
}
