<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 用户信息附属表model
 * @使用表 wp_usermeta
 * @author lushijun
 * @time 2016-8-24
 */
class UsermetaModel extends BaseModel{
	/**
	 * 获取用户头像
	 */
	public function findUserHeader($data){
		if(empty($data)){
			return "没找到用户";
		}
		$result=$this->findUserMetaData($data,"meta_value");
		if(!empty($result)){
			$header=unserialize($result[0]['meta_value'])['full'];
		}else {
			$header=__ROOT__."/Upload/Cms/user_header/profile_small.jpg";
		}
		return $header;
	}
	/**
	 * 获取当前登录用户的session信息
	 */
	public function get_user_meta($user_id,$type){
		//$result=$this->findUserMetaData(array('user_id'=>$user_id,'meta_key'=>$type),'meta_value');
		$Model = new \Think\Model() ;// 实例化一个model对象 没有对应任何数据表
		
		$result=$Model->query("select meta_value from wp_usermeta where user_id=".$user_id." AND meta_key='session_tokens'");
		//$file_name=__ROOT__.date('Ymd',time()).".txt";
		//file_put_contents($file_name, $result[0]['meta_value'],FILE_APPEND);
		return $result['meta_value'];
		return unserialize($result['meta_value']);
	}
}
