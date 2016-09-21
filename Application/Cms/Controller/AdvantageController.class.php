<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
/**
 * 元素空间 优势标签
 * @使用表 kj_advantage_info
 * @author lushijun
 * @time 2016-8-25
 */
class AdvantageController extends CmsBaseController{
	/**
	 * 标签首页
	 */
	public function index(){
		//查询数据标签
		$where['search_name']=I('search_name');
		$where['is_use']=I('is_use');
		$where['is_del']=I('is_del');
		$list=D('AdvantageInfo')->getAdvantageList($where);
		$this->assign('list',$list);
		$this->display();
	}
	/**
	 * 添加标签页面
	 */
	public function add(){
		$this->display();
	}
	/**
	 * 添加标签操作
	 */
	public function insert(){
		if(IS_POST){
				$arr=array(
						'label_name'=>I('label_name'),
						'sortord'=>I('sortord'),
						'is_del'=>I('is_del'),
						'is_use'=>I('is_user'),
						'add_time'=>time()
				);
				$result=D('AdvantageInfo')->insertAdvantageInfo($arr);
				if($result['code'] =='200'){
					$this->success('空间标签添加成功','index');
				}else{
					$this->error($result['msg'],'add');
				}
		}else{
			$this->error('错误操作','add');
		}
	}
	/**
	 * 修改页面
	 */
	public function edit(){
		$id=I('id');
		$list=D('AdvantageInfo')->findAdvantageRow($id);
		if($list['code'] =='200'){
			$this->assign('list',$list['msg']);
			$this->display();
		}else{
			$this->error($list['msg'],'index');
		}
	
	}
	/**
	 * 修改标签操作
	 */
	public function update(){
		if(IS_POST){
			$arr=array(
					'label_name'=>I('label_name'),
					'sortord'=>I('sortord'),
					'is_del'=>I('is_del'),
					'is_use'=>I('is_use'),
					'add_time'=>time()
			);
			$result=D('AdvantageInfo')->updateAdvantageInfo($arr,I('id'));
			if($result['code'] =='200'){
				$this->success('空间标签修改成功','index');
			}else{
				$this->error($result['msg'],'edit/id/'.I('id'));
			}
		}else{
			$this->error('错误操作','edit/id/'.I('id'));
		}
	}
	/**
	 * 删除标签操作
	 */
	public function del(){
		if (IS_GET){
			$result=D('AdvantageInfo')->delAdvantageInfo(I('id'));
			if($result['code'] =='200'){
				$this->success('空间标签删除成功',U('Cms/Advantage/index'));
			}else{
				$this->error($result['msg'],U('Cms/Advantage/index'));
			}
		}else{
			$this->error('错误操作',U('Cms/Advantage/index'));
		}
	}
}
