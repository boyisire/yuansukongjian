<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 申请入住操作model
 * @使用表 cms_account
 */
class entry_checkModel extends BaseModel{

	/**
	 * 查看申请入住页面列表
	 * @param   array           当前用户id
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-17
	 */
	public function showList($where){
		$showList = $this->selectPageData('',$where,'add_time desc','10');
		foreach ($showList['list'] as $k=>$v){
			//用户名
			$showList['list'][$k]['user_name'] = D('RoomInfo')->getUserInfo($v['user_id'])['user_nicename']?D('RoomInfo')->getUserInfo($v['user_id'])['user_nicename']:'-';
			//用户手机号
			$showList['list'][$k]['user_phone'] = D('RoomInfo')->getUserInfo($v['user_id'])['user_mobile']?D('RoomInfo')->getUserInfo($v['user_id'])['user_mobile']:'-';
			//关于空间用户的，空间名称，空间联系人，联系人手机号
			$condition = 'id='.$v['room_id'];
			$showList['list'][$k]['room_name'] = D('RoomInfo')->getInfo($condition)['list'][0]['room_name']?D('RoomInfo')->getInfo($condition)['list'][0]['room_name']:'-';
			$showList['list'][$k]['room_user'] = D('RoomInfo')->getInfo($condition)['list'][0]['room_linkman']?D('RoomInfo')->getInfo($condition)['list'][0]['room_linkman']:'-';
			$showList['list'][$k]['room_phone'] = D('RoomInfo')->getInfo($condition)['list'][0]['room_mobile']?D('RoomInfo')->getInfo($condition)['list'][0]['room_mobile']:'-';
			//超时操作
//			dump($this->time_tran($v['add_time']));
			$showList['list'][$k]['overtime'] = $this->time_tran($v['add_time']);
		}
		return $showList;
	}

	/**
	 * 超时操作
	 */
	private function time_tran($the_time){
		$now_time = date("Y-m-d H:i:s",time());
		$now_time = strtotime($now_time);
//		$show_time = strtotime($the_time);
		$show_time = $the_time;
		$dur = $now_time - $show_time;
		if($dur < 0){
			return $the_time;
		}else{
			if($dur < 60){
				return $dur.'秒';
			}else{
				if($dur < 3600){
					return floor($dur/60).'分钟';
				}else{
					if($dur < 86400){
						return floor($dur/3600).'小时';
					}else{
						if($dur < 259200){//3天内
							return floor($dur/86400).'天';
						}elseif($dur > 259200){
							return "<p style='color:red'>".floor($dur/86400)."天".floor($dur/86400/3600)."小时".floor($dur/86400/3600/60)."分钟</p>";
						}else{
							return "-天-小时-分";
						}
					}
				}
			}
		}
	}
	/**
	 * 查看申请入住详情页面列表
	 * @param   array           当前用户id
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-19
	 */
	public function showInfo($id){
		$condition = "id = ".$id;
		$showInfo['list'] = $this->findData($condition);
		$showInfo['list']['room_name'] = D('RoomInfo')->getInfo($condition)['list'][0]['room_name']?D('RoomInfo')->getInfo($condition)['list'][0]['room_name']:'-';
		$showInfo['list']['room_user'] = D('RoomInfo')->getInfo($condition)['list'][0]['room_linkman']?D('RoomInfo')->getInfo($condition)['list'][0]['room_linkman']:'-';

		//查找备注表内信息
		$conditions['type'] = array('eq','2');
		$conditions['r_id'] = array('eq',$id);
		$showInfo['remark'] = D('operation_subsidiary')->showList($conditions);
		foreach ($showInfo['remark'] as $k => $v){
			$showInfo['remark'][$k]['user_name'] = D('RoomInfo')->getUserInfo($v['user_id'])['user_nicename']?D('RoomInfo')->getUserInfo($v['user_id'])['user_nicename']:'-';
		}
		return $showInfo;
	}

	/**
	 * 修改是否已经联系
	 * @param   array           当前用户id，是否联系,id
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-19
	 */
	public function saveRelation($condition,$save,$id,$uid){
		$isSave = $this->editData($condition,$save);
		$this->addRemark($id,1,$uid);
		return $isSave;
	}

	/**
	 * 修改是否已确认成交
	 * @param   array           id
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-26
	 */
	public function saveConfirmation($id,$uid){
		$condition = "id = ".$id;
		$save['rent_status'] = 4;
		$isSave = $this->editData($condition,$save);
		$this->addRemark($id,1,$uid);
		return $isSave;
	}

	/**
	 * 修改是否通过
	 * @param   array           当前用户id，是否联系,id
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-22
	 */
	public function saveIsPass($id,$pass,$uid){
		$save['rent_status'] = $pass;
		$condition = "id = ".$id;
		$isSave = $this->editData($condition,$save);
		$this->addRemark($id,1);
		$roomId = $this->getFindInfo($condition,'room_id');
		$entryPhone = $this->getFindInfo($condition,'company_phone');
		$roomName = D('RoomInfo')->getRoomInfo($roomId,'room_name');
		if($pass == 2){
			$value = array($roomName);
			$templ = C('SMS_TEMPLATE')['KJZ_Apply_Erro'];
		}else if($pass == 3){
			$value = array($roomName,"不符合孵化器要求");
			$templ = C('SMS_TEMPLATE')['KJZ_Apply_Succ'];
		}
		//手机号+空间名称+模版号
		$sendRes = sendSMS($entryPhone,$value,$templ);
		//dump($sendRes);
		if($sendRes['code'] == 200){
			//查询发送的短信内容:模版号+传入值
			$smsDesc = querySMS($templ,$value);
			$smsData = array(
				'sms_desc'   => $smsDesc,
				'sms_status' => 1,
				'sms_type'   => 2, //操作类型：1-验证码 2-系统消息 3-审核消息
				'accept_user'=> $uid,
				'send_time'  => time(),
			);
			//写入短信记录表
			D('Sms_info')->addData($smsData);
		}
		return $isSave;
	}

	/**
	 * 删除操作
	 * @param   array           当前用户id，是否联系
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-22
	 */
	public function delEntry($condition,$id,$uid){
		$isSave = $this->deleteData($condition);
		if($isSave){
			$entryName = $this->findData($condition,'company_name');
			$this->addRemark($id,2,$uid);
		}
		return $isSave;
	}

	/**
	 * 申请人列表
	 * @param   array           当前用户id，是否联系
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-23
	 */
	public function proposer($where){
		$showList = $this->selectData('user_id,room_id,add_time',$where,'add_time','1');
		foreach($showList as $k=>$v){
			$showList[$k]['room_name'] = D('RoomInfo')->getRoomInfo($v['room_id'],'room_name');
		}
		return $showList;
	}

	/**
	 * 整合数据
	 * @param   array           二维数组 需要修改的数组
	 * @return	boolean			整合后数据
	 * @author  zhanganran
	 * @time    2016-8-23
	 */
	public function concordance($list){
		foreach ($list as $k=>$v){
			$list[$k]['user_name'] = D('RoomInfo')->getUserInfo($v['user_id'])['user_nicename'];
		}
		return $list;
	}
	/**
	 * 查找单独字段信息
	 */
	public function getFindInfo($condition="1=1",$field){
		$condition = $condition;
		$field=$field;
		// 实例化一个model对象 没有对应任何数据表
		$res = $this
			->where($condition)
			->getField($field);
		return $res;
	}

	/**
	 * 查找单条信息
	 */
	public function getFind($id){
		$info = $this->findData('id = '.$id);
		return $info;
	}

	/**
	 * 修改申请入住信息
	 */
	public function saveEntry($save,$uid){
		if($save['temporary']){
			$addQiHuaShu = $this->prospectusUpload($_FILES['company_prospectus']['name']);
			if($addQiHuaShu['result'] == 1){
				$save['company_prospectus'] = $save['temporary']['name'];
				$save['add_time'] = substr($addQiHuaShu['addName'],strrpos($addQiHuaShu['addName'],'_')+1);
			}
		}else{
			$save['company_prospectus'] = '';
			$save['add_time'] = time();
		}
		$save['rent_status'] = 1;
		$condition = "id = ".$save['entryId'];
		$isSave = $this->editData($condition,$save);
		$this->addRemark($save['entryId'],1,$uid);
		return array('result'=>'200','msg'=>'修改成功','id'=>$save['entryId']);
	}
	/**
	 * 添加申请入住信息
	 */
	public function addEntry($save,$uid){
		if($save['temporary']['name']){
			$addQiHuaShu = $this->prospectusUpload($_FILES['company_prospectus']['name']);
			if($addQiHuaShu['result'] == 1){
				$save['company_prospectus'] = $save['temporary']['name'];
				$save['add_time'] = substr($addQiHuaShu['addName'],strrpos($addQiHuaShu['addName'],'_')+1);
			}
		}else{
			unset($save['temporary']);
			$save['company_prospectus'] = '';
			$save['add_time'] = time();
		}
		if($save['rent_type'] == 1){
			unset($save['rent_numbers']);
			$save['rent_number'] = $save['rent_number'];
		}else{
			$save['rent_number'] = $save['rent_numbers'];
			unset($save['rent_numbers']);
		}
		$save['room_user_id'] = D('RoomInfo')->getRoomInfo($save['room_id'],'user_id');
		$condition = "user_id = ".$save['user_id']." and room_id = ".$save['room_id'];
		if($this->findData($condition)){
			@link('/Upload/firmCultureBook/'.$save['temporary']['name']);
			return array('result'=>'401','msg'=>'已申请过');
		}else{
			$assResult = $this->addData($save);
		}
		if($assResult){
			return array('result'=>'200','msg'=>'申请成功','id'=>$assResult);
		}else{
			@link('/Upload/firmCultureBook/'.$save['temporary']['name']);
			return array('result'=>'400','msg'=>'连接服务器失效');
		}
	}
	/**
	 * 执行企业文化书上传的方法
	 * @param 文件名字
	 * @return 上传成功与否
	 */
	private function prospectusUpload($name){
		$newName = substr($name,0,strrpos($name,'.'));
		$upload = new \Think\Upload();// 实例化上传类
		$upload->rootPath  =     "Upload/"; // 设置附件上传根目录
		$upload->savePath  =     'firmCultureBook/'; // 设置附件上传（子）目录
		$upload->saveName  =     urlencode($newName).'_'.time();
		$upload->hash  =     false;
		$upload->autoSub  =     false;
		// 上传文件
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$images['result']="0";
			$images['msg']=$upload->getError();
			return $images;
		}else{// 上传成功
			$images['result']="1";
			$images['msg']="上传成功";
			$images['data']= 'Upload/firmCultureBook/'.$info['company_prospectus']['name'];
			$images['addName']= $upload->saveName;
			return $images;
		}
	}

	/**
	 * 添加备注信息
	 */
	public function addRemark($r_id,$op_type,$uid){
		$add['type'] = 2;
		$add['r_id'] = $r_id;
		$add['user_id'] = $uid;
		$NameUser = M()->table('wp_users')->where(array('ID'=>$add['user_id']))->getField('user_nicename');
		//1-修改 2-删除 3-通过 4-未通过 5-确认成交 6-添加
		$condition = "id = ".$r_id;
		$entryName = $this->findData($condition,'company_name');
		switch ($op_type){
			case 1:
				$type = 1;
				$desc = $NameUser."在".date('Y-m-d H:i:s',time())."修改了".$entryName['company_name']."的申请信息";
				break;
			case 2:
				$type = 2;
				$desc = $NameUser."在".date('Y-m-d H:i:s',time())."删除了".$entryName['company_name']."的申请信息";
				break;
			case 3:
				$type = 3;
				$desc = $NameUser."在".date('Y-m-d H:i:s',time())."通过了".$entryName['company_name']."的申请信息";
				break;
			case 4:
				$type = 4;
				$desc = $NameUser."在".date('Y-m-d H:i:s',time())."未通过了".$entryName['company_name']."的申请信息";
				break;
			case 5:
				$type = 5;
				$desc = $NameUser."在".date('Y-m-d H:i:s',time())."确认成交了".$entryName['company_name']."的申请信息";
				break;
			case 6:
				$type = 6;
				$desc = $NameUser."在".date('Y-m-d H:i:s',time())."添加了".$entryName['company_name']."的申请信息";
				break;
		}
		$add['op_type'] = $type;
		$add['op_desc'] = $desc;
		$add['add_time'] = time();
		$addRemark = D('operation_subsidiary')->addRemark($add);
	}
	/**
	 * 获取分组用户id
	 * @group 分组条件
	 * @type 返回数据类型  1所有 2只返回id 3只返回个数
	 * @return array
	 * @author lushijun
	 * @time 2016-8-24
	 */
	public function getUserIdGroup($field,$where,$order,$group,$type){
		$result=$this->selectGroupData($field,$where,$order,$group);
		if($type =='2'){
			//分解数据，返回
			foreach($result as $val){
				$arr[]=$val['user_id'];
			}
			//return implode(',', $arr);
			 return $arr;
		}elseif($type =='3'){
			//分解数据，返回
			foreach($result as $val){
				$arr[]=$val['num'];
			}
			//return implode(',', $arr);
			return $arr;
		}else{
			return $result;
		}

	}
	/**
	 * 根据用户id获取当前用户入驻的空间个数
	 * @data array
	 * @author lushijun
	 * @time 2016-8-24
	 */
	public function getApplyFromUser($data){
		if(empty($data)){
			return "0";
		}else {
			$count=$this->countData($data);
			if(empty($count)){
				return "0";
			}else{
				return $count;
			}
		}
	}


	/**
	 * [根据用户ID和空间ID获取其状态]
	 * @method getStatus
	 * @author 大业
	 * @create 2016-08-28
	 * @param  [int] $uid [用户ID]
	 * @param  [int] $rid [空间ID]
	 * @return [int] [状态值]
	 */
	public function getStatus($uid,$rid)
	{
		if(empty($uid) || empty($rid)){
			return 999;
		}
		$where = 'user_id = '.$uid.' and room_id = '.$rid;
		$field = 'rent_status';
		$res = $this->findData($where,$field);
		if($res){
			return (int)$res['rent_status'];
		}else{
			return 0;
		}
	}

}
