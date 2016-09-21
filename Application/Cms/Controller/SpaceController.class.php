<?php
namespace Cms\Controller;
use Common\Controller\CmsBaseController;
/**
 * 后台首页控制器
 */
class SpaceController extends CmsBaseController{
	/**
	 * 首页
	 */
	public function index(){
		// 分配菜单数据
		$this->display();
	}

	/**
	* 申请入住列表页面
	*/
	public function entryCheck(){
		if($_REQUEST['sh']){
			$where['rent_status'] = $_REQUEST['sh'];
		}else if($_REQUEST['rid']){
			$where = 'room_id = '.$_REQUEST['rid'];
		}else{
			$where = '1=1';
		}
		$showList = D('entry_check')->showList($where);
		$this->assign('showList',$showList['list']);
		$this->assign('showPage',$showList['page']);
		$this->display();
	}

	/**
	 * 申请入住详情页面
	 */
	public function projectDetail(){
		$id = I('id');
		if($id){
			$showInfo = D('entry_check')->showInfo($id);
			$this->assign('showInfo',$showInfo['list']);
			$this->assign('showInfoRemark',$showInfo['remark']);
		}else{
			$this->error('参数错误',U('Space/entryCheck'));
		}
		$this->display();
	}

	/**
	 * 提交备注
	 */
	public function getRemark(){
		$post = I();
		if($post){
			$post['optionsRadios'] = $post['optionsRadios'] == 1?$post['optionsRadios']:'0';
			$condition = "id = ".$post['productId'];
			$save['relation_status'] = $post['optionsRadios'];
			$saveRelation = D('entry_check')->saveRelation($condition,$save,$post['productId'],$this->uid);
			$add['type'] = 2;
			$add['r_id'] = $post['productId'];
			$add['user_id'] = $this->uid;
			$add['op_type'] = '6';
			$add['op_desc'] = $post['remark'];
			$add['add_time'] = time();
			$addRemark = D('operation_subsidiary')->addRemark($add);
			if($addRemark){
				$this->success('备注成功',U("Cms/Space/projectDetail/id/{$_POST['productId']}"));
			}else{
				$this->error('备注失败',U("Cms/Space/projectDetail/id/{$_POST['productId']}"));
			}
		}else{
			$this->error('参数错误',U("Cms/Space/projectDetail/id/{$_POST['productId']}"));
		}
	}

	/**
	 * 修改申请通过不通过
	 */
	public function saveIsPass(){
		$id = I('id');
		if($id){
			$showInfo = D('entry_check')->saveIsPass($id,I('pass'),$this->uid);
			if($showInfo){
				$this->success('修改成功',U('Space/entryCheck'));
			}else{
				$this->error('修改失败',U('Space/entryCheck'));
			}
		}else{
			$this->error('参数错误',U('Space/entryCheck'));
		}

	}
	/**
	 * 执行删除操作
	 */
	public function delEntry(){
		if($_SESSION['s_uinfo']['user_power'] == 100){
			$id = (int)I('id');
			if($id){
				$condition = "id = ".$id;
				$showInfo = D('entry_check')->delEntry($condition,$id,$this->uid);
				if($showInfo){
					$this->success('删除成功',U('Space/entryCheck'));
				}else{
					$this->error('删除失败',U('Space/entryCheck'));
				}
			}else{
				$this->error('参数错误',U('Space/entryCheck'));
			}
		}else{
			$this->error('您没有删除的权限',U('Space/entryCheck'));
		}
	}

	/**
	 * 备注页面
	 */
	public function remarkPage(){
		$id = (int)I('id');
		if($id){
			$this->assign('id',$id);
		}else{
			$this->error('参数错误',U('Space/entryCheck'));
		}
		$this->display();
	}

//	/**
//	 * 申请人列表
//	 */
//	public function proposer(){
//		$where = '1=1';
//		$showCheryList = D('entry_check')->proposer($where);
//		$showRoomList = D('RoomInfo')->getRoomsInfo($where,'user_id,id,room_name,add_time,id');
//		$showListTranche = array_merge($showCheryList,$showRoomList);
//		$showList = D('entry_check')->concordance($showListTranche);
//		$this->assign('showList',$showList);
//		$this->display();
//	}
//
//	/**
//	 * roomDetail
//	 */
//	public function roomDetail(){
//		echo "<h1 style='text-align: center;margin-top: 500px;color: red'>暂未开通</h1>";die;
//	}

	/**
	 * 下载文件
	 */
	public function downUserFile(){
		$id= I('id');
		$info = D('entry_check')->getFindInfo('id='.$id,'company_prospectus');
		if(!empty($info)){
			$isDown = downFile($info);
		}else{
			echo "<script>alert('暂无商业计划书');</script>";
		}
	}

	/**
	 * saveEntry  修改申请
	 */
	public function saveEntry(){
		$id = I('id');
		$info = D('entry_check')->getFind($id);
		$this->assign('info',$info);
		$this->display();
	}

	/**
	 * 执行修改申请
	 */
	public function doSaveEntry(){
		$save = I();
		$save['temporary'] = $_FILES['company_prospectus'];
		$info = D('entry_check')->saveEntry($save,$this->uid);
		if($info['result'] == 400){
			$this->error($info['msg'],U("Cms/Space/saveEntry/id/{$save['entryId']}"));
		}else{
			$this->success($info['msg'],U("Cms/Space/projectDetail/id/{$save['entryId']}"));
		}
	}

	public function confirmation(){
		$id = I('id');
		$info = D('entry_check')->saveConfirmation($id,$this->uid);
//		if($info['result'] == 400){
//			$this->error('修改失败',"entryCheck");
//		}else{
			$this->success('修改成功',U('Space/entryCheck'));
//		}
	}

}
