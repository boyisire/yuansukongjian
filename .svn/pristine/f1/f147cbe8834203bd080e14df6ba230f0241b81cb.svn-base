<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
use Common\Model\UsersModel;
/**
 * 短信管理
 * @使用表  kj_sms_info
 * @author lusijun
 * @time 2016-8-19
 */
class SendsmsController extends CmsBaseController{
	/**
	 * 首页
	 */
	public function index(){
		//获取所有短信信息
		$data['like']=I('like_content');
		$data['sms_type']=I('sms_type');
		$res=D('Sms_info')->sendSmsSelect($data);
		
		//切换usersModel--表前缀为wp_,上传要引入usermodel-use Common\Model\UsersModel;
		$User = new UsersModel('users','wp_','connection');
		foreach($res['list'] as $key=>$val){
			//获取当前接受短信的用户名称
			$res['list'][$key]['user_name']=$User->findUser(array('ID'=>$val['accept_user']));
		}
		$this->assign('list',$res);
		$this->display();
	}
	/**
	 * 发送短信页面
	 */
	public function add(){
		$this->display();
	}
	/**
	 * 发送短信操作
	 */
	public function addSend(){
		if(IS_POST){			
			//根据用户id获取用户电话
			$User = new UsersModel('users','wp_','connection');
			$user_phone=$User->findUserPhone(I('accept_user'));
			$data=array(
					'accept_user'=>$user_phone,
					'sms_type'=>I('sms_type'),
					'sms_desc'=>I('sms_desc'),
					'send_time'=>time()
			);
			$res=D('Sms_info')->sendUserSms($data);
			var_dump($user_phone);
		}else{
			$this->error('错误操作','add');
		}
	}
}
