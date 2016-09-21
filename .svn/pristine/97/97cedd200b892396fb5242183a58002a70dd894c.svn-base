<?php
namespace Cms\Controller;
use Common\Model\RoomInfoModel;
use Common\Model\entry_checkModel;
use Common\Model\UsersModel;
use Common\Model\UsermetaModel;
use Common\Controller\CmsBaseController;
/**
 * 所有申请空间和申请入驻的用户信息
 * @使用表 wp_users ,kj_room_info ,kj_entry_check
 * @author lushijun
 * @time 2016-8-24
 */
class UsersController extends CmsBaseController{
	/**
	 * 用户列表首页
	 */
	public function index(){
		$page=empty(I('page'))?1:I('page');
		//获取当前所有空间信息//切换model
		$room_user_id=D('RoomInfo')->getUserIdGroup('user_id,count(user_id) as num','','','user_id',1);
		$apply_user_id=D('entry_check')->getUserIdGroup('user_id,count(user_id) as num','','','user_id',1);
		//合并去除重复
		$arr=$this->arr_array_merge($room_user_id,$apply_user_id);
		//切换表前缀,获取所有用户信息
		$User = new UsersModel('users','wp_','connection');
		$Usermeta=new UsermetaModel('usermeta','wp_','connection');
		$result=$User->findUserFromId($arr);
		//获取发送空间或者入驻空间个数//用户头像
		if(!empty($result)){
			foreach($result as $key=>$val){
				$result[$key]['room_num']=D('RoomInfo')->getRoomFromUser(array('user_id'=>$val['id']));
				$result[$key]['apply_num']=D('entry_check')->getApplyFromUser(array('user_id'=>$val['id']));
				$result[$key]['user_header']=$Usermeta->findUserHeader(array('user_id'=>$val['id'],'meta_key'=>'simple_local_avatar'));
			}
		}
		$totals="12";
		$url=U('Cms/Users/index');
		$count=count($result);
		$countpage=ceil($count/$totals); #计算总页面数
		$result=$this->page_array($totals,$page,$result,'2');
		$show_page=$this->show_array($countpage,$url,$page);
		$this->assign('show_page',$show_page);
		$this->assign('list',$result);
		$this->display();
	}
	/**
	 * 合并去除重复
	 */
	private function arr_array_merge($room,$apply){
		if(!empty($room) && !empty($apply)){
			$arr = array_unset(array_merge($room,$apply),'user_id');
		}else{
			if(empty($room)){
				$arr=$apply;
			}else{
				$arr=$room;
			}
		}
		foreach($arr as $val){
			$arr1[]=$val['user_id'];
		}
		return $arr1;
		
	}
	/**
	 * 数组分页函数 核心函数 array_slice
	 * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
	 * $count  每页多少条数据
	 * $page  当前第几页
	 * $array  查询出来的所有数组
	 * order 0 - 不变   1- 反序
	 */
	private function page_array($count,$page,$array,$order){
		$page=(empty($page))?'1':$page; #判断当前页面是否为空 如果为空就表示为第一页面
		$start=($page-1)*$count; #计算每次分页的开始位置
		if($order==1){
			$array=array_reverse($array);
		}
		$totals=count($array);
		$countpage=ceil($totals/$count); #计算总页面数
		$pagedata=array();
		$pagedata=array_slice($array,$start,$count);
		return $pagedata; #返回查询数据
	}
	/**
	 * 分页及显示函数
	 * $countpage 全局变量，照写
	 * $url 当前url
	 */
	private function show_array($countpage,$url,$page){
		if($page > 1){
			$uppage=$page-1;
		}else{
			$uppage=1;
		}
		if($page < $countpage){
			$nextpage=$page+1;
	
		}else{
			$nextpage=$countpage;
		}
		$str='<div style="border:1px; width:300px; height:30px; color:#9999CC">';
		$str.="<span>共 {$countpage} 页 / 第 {$page} 页</span>";
		$str.="<span><a href='$url?page=1'>  首页 </a></span>";
		$str.="<span><a href='$url?page={$uppage}'> 上一页 </a></span>";
		$str.="<span><a href='$url?page={$nextpage}'>下一页 </a></span>";
		$str.="<span><a href='$url?page={$countpage}'>尾页 </a></span>";
		$str.='</div>';
		return $str;
	}
}
