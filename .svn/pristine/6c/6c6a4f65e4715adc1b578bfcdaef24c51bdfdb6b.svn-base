<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
/**
 * 所有申请空间和申请入驻的用户信息
 * @使用表 kj_activity
 * @author lushijun
 * @time 2016-8-30
 */
class ActivityController extends CmsBaseController{
	/**
	 * 活动列表
	 */
	public function index(){
		$data['search_name']=I('search_name');
		$data['is_show']=I('is_show');
		$result=D('Activity')->selectAllPageData($data);
		$this->assign('result',$result);
		$this->display();
	}
	/**
	 * 活动添加页面
	 */
	public function add(){
		$this->display();
	}
	/**
	 * 获取添加操作
	 */
	public function insertAct(){
		if(IS_POST){
			$data=array(
					'act_name'=>I('act_name'),
					'is_time'=>I('is_time'),
					'start_time'=>strtotime(I('start_time')),
					'end_time'=>strtotime(I('end_time')),
					'start_time2'=>strtotime(I('start_time2')),
					'end_time2'=>strtotime(I('end_time2')),
					'is_sort'=>I('is_show'),
					'is_show'=>I('is_show'),
					'add_time'=>time()
			);
			$result=D('Activity')->addAct($data);
			if($result['code'] =='200'){
				$this->success('活动添加成功','add');
			}else{
				$this->error('活动添加失败','add');
			}
		}else{
			$this->error('错误操作','add');
		}
	}
	/**
	 * 活动修改页面
	 */
	public function edit(){
		$id=I('id');
		if(!empty($id)){
			$result=D('Activity')->findData(array('id'=>$id));
			$this->assign('result',$result);
			$this->display();
		}else{
			$this->error('错误操作','index');
		}
	}
	/**
	 * 修改操作
	 */
	public function updateAct(){
		if(IS_POST){
			$id=I('id');
			if(!empty($id)){
				$data=array(
						'act_name'=>I('act_name'),
						'is_time'=>I('is_time'),
						'start_time'=>strtotime(I('start_time')),
						'end_time'=>strtotime(I('end_time')),
						'start_time2'=>strtotime(I('start_time2')),
						'end_time2'=>strtotime(I('end_time2')),
						'is_sort'=>I('is_show'),
						'is_show'=>I('is_show')
				);
				$result=D('Activity')->editAct($data,$id);
				if($result['code'] =='200'){
					$this->success('活动修改成功','index');
				}else{
					$this->error('活动修改失败','edit/id/'.$id);
				}
			}else{
				$this->error('错误操作','index');
			}
		}else{
			$this->error('错误操作','index');
		}
	}
	/**
	 * 删除活动操作
	 */
	public function del(){
		$data['id']=I('id');
		$result=D('Activity')->delAct($data);
		if($result['code'] =='200'){
			$this->success('活动删除成功',U('Cms/Activity/index'));
		}else{
			$this->error($result['msg'],U('Cms/Activity/index'));
		}
	}
}
