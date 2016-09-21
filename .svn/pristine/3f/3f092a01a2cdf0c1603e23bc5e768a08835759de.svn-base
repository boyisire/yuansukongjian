<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
/**
 * 亚杰活动战队信息表
 * @使用表 kj_activity,kj_activity_info
 * @author lushijun
 * @time 2016-8-30
 */
class ActivityInfoController extends CmsBaseController{
	/**
	 * 战队信息表
	 */
	public function index(){
		//获取所有战队信息
		$data['is_show']=I('is_show');
		$data['search_name']=I('search_name');
		$data['act_id']=I('act_id');
		$result=D('Activity_info')->selectCorpsPageData($data);
	
		//获取对应活动名称
		if(!empty($result['list'])){
			foreach($result['list'] as $key=>$val){
				$result['list'][$key]['act_name']=D('Activity')->findActivityData(array('id'=>$val['act_id']));
				//获取战队成员信息
				$result['list'][$key]['child']=D('Activity_info_user')->selctCorpsMessage(array('cid'=>$val['id']));
				$votes[]=$val['corps_votes'];
				$fb[]=$val['corps_fb'];
			}
		}
		//当前票总数
		$all_votes=array_sum($votes);
		//当前分贝总数
		$all_fb=array_sum($fb);
		//计算当前战队分数
		foreach($result['list'] as $key=>$val){
			$result['list'][$key]['votes_val']=round(($val['corps_votes']/$all_votes)*20,2);
			$result['list'][$key]['fb_val']=round(($val['corps_fb']/$all_fb)*80,2);
			$result['list'][$key]['all_val']=$result['list'][$key]['votes_val']+$result['list'][$key]['fb_val'];
		}
		$this->assign('result',$result);
		//获取所有活动id
		$this->display();
	}
	/**
	 * ajax 操作分贝值
	 */
	public function checkCorpsFb(){
		$data=array(
				'id'=>I('id'),
				'corps_fb'=>I('corps_fb')
		);
		//获取当前分贝值
		$votes=D('Activity_info')->findData(array('id'=>$data['id']),'corps_fb');
		$result=D('Activity_info')->ajaxCheckFb($data,I('type'),$votes['corps_fb']);
		$this->ajaxReturn($result);
	}
	/**
	 * 战队投票图表
	 */
	public function echart(){
		//获取所有战队信息
		$result=D('Activity_info')->selectActivityData(array('is_show'=>'1'));
		//获取真实票数
		foreach ($result as $key=>$val){
			$result[$key]['real_corps']=D('Activity_votes')->realCorps(array('cid'=>$val['act_id'],'aid'=>$val['id']));
		}
		$votes=array();
		foreach($result as $val){
			$votes[]=$val['corps_votes'];
			$corps_name[]="'".$val['corps_name']."'";
			$real_corps[]=$val['real_corps'];
		}
		$this->assign('real_corps',implode(',',$real_corps));
		$this->assign('votes',implode(',',$votes));
		$this->assign('corps_name',implode(',',$corps_name));
		$this->display();
	}
	/**
	 * 用户分析表
	 */
	public function user_echart(){
		$time_status=I('time_status');
		//获取当前所有用户分析数据
		$count=D('Activity_user_count')->selectUserCountGroup();
		//循环获取字符串
		foreach ($count as $key=>$val){
			if($val['time_status'] =='1'){
				$arr['canyuyonghu']=$val['num'];
			}
			if($val['time_status'] =='2'){
				$arr['guanzhuweixin']=$val['num'];
			}
			if($val['time_status'] =='3'){
				$arr['dianjitoupiao']=$val['num'];
			}
			if($val['time_status'] =='4'){
				$arr['tianxieshouji']=$val['num'];
			}
			if($val['time_status'] =='5'){
				$arr['querentoupiao']=$val['num'];
			}
		}
		if(empty($arr['canyuyonghu'])){
			$arr['canyuyonghu']="0";
		}
		if(empty($arr['guanzhuweixin'])){
			$arr['guanzhuweixin']="0";
		}
		if(empty($arr['dianjitoupiao'])){
			$arr['dianjitoupiao']="0";
		}
		if(empty($arr['tianxieshouji'])){
			$arr['tianxieshouji']="0";
		}
		if(empty($arr['querentoupiao'])){
			$arr['querentoupiao']="0";
		}
		$arr['number']['canyuyonghu']=(round($arr['canyuyonghu']/$arr['canyuyonghu'],2))*100;
		$arr['number']['guanzhuweixin']=(round($arr['guanzhuweixin']/$arr['canyuyonghu'],2))*100;
		$arr['number']['dianjitoupiao']=(round($arr['dianjitoupiao']/$arr['canyuyonghu'],2))*100;
		$arr['number']['tianxieshouji']=(round($arr['tianxieshouji']/$arr['canyuyonghu'],2))*100;
		$arr['number']['querentoupiao']=(round($arr['querentoupiao']/$arr['canyuyonghu'],2))*100;
		//获取所有数据
		$list=D('Activity_user_count')->selectUserCountData($time_status);
		$this->assign('result',$list);
		$this->assign('count',$arr);
		$this->display();
	}
	/**
	 * ajax 刷新获取最新票数
	 */
	public function ajaxIndex(){
		$result=D('Activity_info')->selectActivityData(array('is_show'=>'1'),'corps_votes,id,act_id');
		$votes=array();
		//获取真实票数
		foreach ($result as $key=>$val){
			$result[$key]['real_corps']=D('Activity_votes')->realCorps(array('cid'=>$val['act_id'],'aid'=>$val['id']));
		}
		foreach($result as $val){
			$votes[]=$val['corps_votes'];
			$real_corps[]=$val['real_corps'];
		}
		$data['votes']=$votes;
		$data['real_corps']=$real_corps;
		//$votes=implode(',',$votes);
		$this->ajaxReturn($data);
	}
	/**
	 * 战队信息添加页
	 */
	public function add(){
		$act=D('Activity')->selectActData("id,act_name");
		$this->assign('act',$act);
		$this->display();
	}
	/**
	 * 添加战队信息操作
	 */
	public function addCorps(){
		if(IS_POST){
			$data=array(
					'act_id'=>I('act_id'),
					'corps_name'=>I('corps_name'),
					'is_show'=>I('is_show'),
					'is_sort'=>I('is_sort'),
					'add_time'=>time(),
					'corps_votes'=>I('corps_votes')
			);
			$result=D('Activity_info')->insertCorps($data);
			if($result['code']=='200'){
				$this->success('战队信息添加成功','add');
			}else{
				$this->error($result['msg'],'add');
			}
		}else{
			$this->error('错误操作','add');
		}
	}
	/**
	 * 修改战队信息页面
	 */
	public function edit(){
		$id=I('id');
		$vlist=D('Activity_info')->findActivityInfoData(array('id'=>$id));
		$act=D('Activity')->selectActData("id,act_name");
		$this->assign('act',$act);
		$this->assign('vlist',$vlist);
		$this->display();
	}
	/**
	 * 修改战队信息操作
	 */
	public function editCorps(){
		$id=I('id');
		if(IS_POST){
			$data=array(
					'act_id'=>I('act_id'),
					'corps_name'=>I('corps_name'),
					'is_show'=>I('is_show'),
					'is_sort'=>I('is_sort'),
					'add_time'=>time(),
					'corps_votes'=>I('corps_votes')
			);
			$result=D('Activity_info')->updateCorps(array('id'=>$id),$data,I('old_corps_images'));
			if($result['code']=='200'){
				$this->success('战队信息修改成功',U('Cms/ActivityInfo/index'));
			}else{
				$this->error($result['msg'],U('Cms/ActivityInfo/edit/id/'.$id));
			}
		}else{
			$this->error('错误操作',U('Cms/ActivityInfo/edit/id/'.$id));
		}
	}
	/**
	 * 删除战队信息，并清除成员信息，清除投票信息
	 */
	public function del(){
		$id=I('id');
		if(IS_GET){
			$result=D('Activity_info')->deleteCorps(array('id'=>$id));
			if($result['code'] =='200'){
				//删除成功清除成员信息
				$msg="战队信息删除成功";
				$member=D('Activity_info_user')->delMemberCorps(array('cid'=>$id));
				$votes=D('Activity_votes')->delCorpsVotes(array('cid'=>$id));
				$this->success($msg,U('Cms/ActivityInfo/index'));
			}else{
				$this->error($result['msg'],U('Cms/ActivityInfo/index'));
			}
		}else{
			$this->error('错误操作',U('Cms/ActivityInfo/index'));
		}
	}
	/**
	 * ajax 操作票数
	 */
	public function checkVotes(){
		$data=array(
				'id'=>I('id'),
				'corps_votes'=>I('votes')
		);
		//获取当前票数
		$votes=D('Activity_info')->findData(array('id'=>$data['id']),'corps_votes');
		$result=D('Activity_info')->ajaxCheckVotes($data,I('type'),$votes['corps_votes']);
		$this->ajaxReturn($result);
	}
	/**
	 * 添加战队成功页面
	 */
	public function addMember(){
		$id=I('id');
		//根据id获取战队信息
		$list=D('Activity_info')->findData(array('id'=>$id),'id,corps_name');
		$this->assign('list',$list);
		$this->display();
	}
	/**
	 * 添加战队成员操作
	 */
	public function insertMember(){
		if(IS_POST){
			$data=array(
					'cid'=>I('cid'),
					'user_name'=>I('user_name'),
					'is_show'=>I('is_show'),
					'is_sort'=>I('is_sort'),
					'add_time'=>time(),
					'user_dec'=>I('user_dec')
			);
			$result=D('Activity_info_user')->inAddMember($data);
			if($result['code'] =='200'){
				$this->success('战队成功添加成功',U('Cms/ActivityInfo/index'));
			}else{
				$this->error($result['msg'],U('Cms/ActivityInfo/addMember/id/'.I('cid')));
			}
		}else{
			$this->error('错误操作',U('Cms/ActivityInfo/addMember/id/'.I('cid')));
		}
	}
	/**
	 * 修改战队成功页面
	 */
	public function editMember(){
		$id=I('id');
		$cid=I('cid');
		$corps=D('Activity_info')->findData(array('id'=>$id),'id,corps_name');
		$list=D('Activity_info_user')->findData(array('id'=>$id,'cid'=>$cid),'');
		$this->assign('list',$list);
		$this->assign('corps',$corps);
		$this->display();
	}
	/**
	 * 修改战队成功操作
	 */
	public function updateMember(){
		if(IS_POST){
			$data=array(
					'user_name'=>I('user_name'),
					'is_show'=>I('is_show'),
					'is_sort'=>I('is_sort'),
					'add_time'=>time(),
					'user_dec'=>I('user_dec')
			);
			$result=D('Activity_info_user')->inUpdateMember($data,array('id'=>I('id'),'cid'=>I('cid')));
			if($result['code'] =='200'){
				$this->success('战队成功添加成功',U('Cms/ActivityInfo/index'));
			}else{
				$this->error($result['msg'],U('Cms/ActivityInfo/editMember/id/'.I('id').'/cid/'.I('cid')));
			}
		}else{
			$this->error('错误操作',U('Cms/ActivityInfo/editMember/id/'.I('id').'/cid/'.I('cid')));
		}
	}
	/**
	 * 删除战队成员
	 */
	public function delMember(){
		if(IS_GET){
			$result=D('Activity_info_user')->deleteData(array('id'=>I('id')));
			if($result){
				$this->success('战队成员删除成功',U('Cms/ActivityInfo/index'));
			}else{
				$this->error('战场成员删除失败',U('Cms/ActivityInfo/index'));
			}
		}else{
			$this->error('错误操作',U('Cms/ActivityInfo/index'));
		}
	}
}
