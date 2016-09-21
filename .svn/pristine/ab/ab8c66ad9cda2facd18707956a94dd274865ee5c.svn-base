<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 战队投票信息表model
 * @使用表 kj_activity_votes
 * @author lushijun
 * @time 2016-8-31
 */
class Activity_votesModel extends BaseModel{
		
		/**
		 * 删除战队下的投票信息
		 */
	public function delCorpsVotes($where){
		return $this->deleteData($where);
	}
	/**
	 * 获取当前用户的投票记录
	 */
	public function findUserVotesData($where,$field){
		return $this->findData($where,$field,'add_time asc');
	}
	/**
	 * 开始投票操作
	 */
	public function insertUserVotes($data){
		if(empty($data['user_id']) || empty($data['cid'])){
			return array('result'=>'1','data'=>'错误操作');
		}
		if(empty($data['phone']) || empty($data['id'])){
			return array('result'=>'1','data'=>'投票失败');
		}
		$arr=array(
				'cid'=>$data['cid'],
				'aid'=>$data['id'],
				'user_id'=>$data['user_id'],
				'phone'=>$data['phone'],
				'ip'=>get_client_ip(),
				'add_time'=>time()
		);
		$result=$this->addData($arr);
		if($result){
			return array('result'=>'2','data'=>'成功');
		}else{
			return array('result'=>'1','data'=>'投票失败');
		}
	}
	/**
	 * 根据战队id或活动id获取真实票数
	 */
	public function realCorps($data){
		$result=$this->countData($data);
		if(empty($result)){
			
			return "0";
		}else{
			return $result;
		}
	}
}
