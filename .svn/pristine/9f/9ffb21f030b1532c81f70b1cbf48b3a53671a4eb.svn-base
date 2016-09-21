<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 亚杰活动战队信息表model
 * @使用表 kj_activity_info
 * @author lushijun
 * @time 2016-8-30
 */
class Activity_infoModel extends BaseModel{
	
		/**
		 * 添加活动信息操作
		 */
	public function insertCorps($data){
		if(empty($data['corps_name'])){
			return array('code'=>'201','msg'=>'战队名称不能为空');
		}
		if(empty($data['act_id'])){
			return array('code'=>'202','msg'=>'活动id不能为空');
		}
		if(empty($data['corps_votes'])){
			$data['corps_votes']="1";
		}
		$path="./Upload/Cms/activity_info/";
		$uploads=upload_image($path);
		if(!empty($uploads)){
			$data['corps_images']=$uploads[0];
		}else{
			return array('code'=>'203','上传战队图片失败');
		}
		$result=$this->addData($data);
		if($result){
			return array('code'=>'200','成功');
		}else{
			return array('code'=>'204','战队信息添加失败');
		}
	}
	/**
	 * 修改战队信息操作
	 */
	public function updateCorps($where,$data,$images){
		if(empty($data['corps_name'])){
			return array('code'=>'201','msg'=>'战队名称不能为空');
		}
		if(empty($data['act_id'])){
			return array('code'=>'202','msg'=>'活动id不能为空');
		}
		if(empty($data['corps_votes'])){
			$data['corps_votes']="1";
		}
		$path="./Upload/Cms/activity_info/";
		$uploads=upload_image($path);
		if(!empty($uploads)){
			$data['corps_images']=$uploads[0];
		}else{
			$data['corps_images']=$images;
		}
		$result=$this->editData($where,$data);
		if($result){
			return array('code'=>'200','成功');
		}else{
			return array('code'=>'204','战队信息修改失败');
		}
	}
	/**
	 * 删除战队信息
	 */
	public function deleteCorps($where){
		return $this->deleteData($where);
	}
	/**
	 * 获取战队信息
	 */
	public function selectCorpsPageData($data){
		if(!empty($data['act_id'])){
			$where['act_id']=array('eq',$data['act_id']);
		}
		if(!empty($data['search_name'])){
			$where['corps_name']=array('like',"%".$data['search_name']."%");
		}
		if(!empty($data['is_show'])){
			$where['is_show']=array('eq',$data['is_show']);
		}
		$result=$this->selectPageData('',$where,'corps_votes desc',10);
		return $result;
	}
	/**
	 * ajax 操作票数
	 */
	public function ajaxCheckVotes($data,$type,$votes){
		if(empty($data['id'])){
			return array('msg'=>'错误操作，没找到id','code'=>'201');
		}
		if(empty($data['corps_votes'])){
			return array('code'=>'202','msg'=>'提交的票数不能为空');
		}
		if(empty($type) || $type =='1'){
			$votes=$votes+$data['corps_votes'];
		}else{
			if($data['corps_votes'] >= $votes){
				$votes='0';
			}else{
				$votes=$votes-$data['corps_votes'];
			}
		}
		$result=$this->editData(array('id'=>$data['id']), array('corps_votes'=>$votes));
		if($result){
			//获取当前所有票数
			$list=$this->selectData('id','','corps_votes desc');
			foreach($list as $key=>$val){
				if($val['id'] == $data['id']){
					$sort=$key+1;
				}
			}
			return array('code'=>'200','msg'=>'成功','data'=>$votes,'id'=>$data['id'],'sort'=>$sort);
		}else{
			return array('code'=>'203','msg'=>'变更战队票数失败');
		}
	}
	/**
	 * ajax 操作分贝值
	 */
	public function ajaxCheckFb($data,$type,$votes){
		if(empty($data['id'])){
			return array('msg'=>'错误操作，没找到id','code'=>'201');
		}
		if(empty($data['corps_fb'])){
			return array('code'=>'202','msg'=>'提交的分贝值不能为空');
		}
		$votes=$votes+$data['corps_fb'];
		$result=$this->editData(array('id'=>$data['id']), array('corps_fb'=>$votes));
		if($result){
			return array('code'=>'200','msg'=>'成功','data'=>$votes);
		}else{
			return array('code'=>'203','msg'=>'变更战队分贝值失败');
		}
	}
	/**
	 * 获取一条战队信息
	 */
	public function findActivityInfoData($data){
		return $this->findData($data, '*');
	}
	/**
	 * 获取所有战队信息
	 */
	public function selectActivityData($where,$feild){
		if(empty($feild)){
			$feild="";
		}
		return $this->selectData($feild,$where,'is_sort asc');
	}
}
