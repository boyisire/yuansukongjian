<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
/**
 * 后台首页控制器
 */
class IndexController extends CmsBaseController{
	/**
	 * 首页
	 */
	public function index(){
		// 分配菜单数据
		$this->display();
	}
	/**
	 * 用户退出操作
	 */
	public function loginOut(){
		if($this->is_login =='1'){
			session('[destroy]');
			$this->redirect('Cms/Login/index');
		}
	}
}
