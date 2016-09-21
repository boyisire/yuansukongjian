<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 活动表model
 * @使用表kj_activity
 * @author lushijun
 * @time 2016-8-30
 */
class ActivityModel extends BaseModel{
	/**
	 * 获取添加活动信息
	 */
	public function addAct($data){
		if(empty($data['act_name'])){
			return array('code'=>'201','活动名称不能为空');
		}
		if(empty($data['is_sort'])){
			return array('code'=>'202','活动顺序不能为空');
		}
		$result=$this->addData($data);
		if($result){
			return array('code'=>'200','msg'=>"成功");
		}else{
			return array('code'=>'203','msg'=>'活动添加失败');
		}
	}
	/**
	 * 获取所有活动信息
	 */
	public function selectAllPageData($data){
		if(!empty($data['search_name'])){
			$where['act_name']=array('like',"%".$data['search_name']."%");
		}
		if(!empty($data['is_show'])){
			$where['is_show']=array('eq',$data['is_show']);
		}
		$result=$this->selectPageData('',$where,'','');
		return $result;
	}
	/**
	 * 修改活动信息
	 */
	public function editAct($data,$id){
		if(empty($data['act_name'])){
			return array('code'=>'201','活动名称不能为空');
		}
		if(empty($data['is_sort'])){
			return array('code'=>'202','活动顺序不能为空');
		}
		$result=$this->editData(array('id'=>$id),$data);
		if($result){
			return array('code'=>'200','msg'=>"成功");
		}else{
			return array('code'=>'203','msg'=>'活动修改失败');
		}
	}
	/**
	 * 删除活动操作
	 */
	public function delAct($data){
		if(empty($data['id'])){
			return array('code'=>'201','msg'=>'错误操作');
		}
		$result=$this->deleteData(array('id'=>$data['id']));
		if($result){
			return array('code'=>'200','活动删除成功');
		}else{
			return array('code'=>'202','活动删除失败');
		}
	}
	/**
	 * 获取所有活动不需要分页
	 */
	public function selectActData($field){
		$where['is_show']=array('eq','1');
		return $result=$this->selectData($field,$where,'is_sort asc');
	}
	/**
	 * 获取活动名称
	 */
	public function findActivityData($data){
		if(empty($data)){
			return "没找到活动";
		}
		$row=$this->findData($data, 'act_name');
		if(empty($row)){
			return "没找到活动";
		}else{
			return $row['act_name'];
		}
	}

}
