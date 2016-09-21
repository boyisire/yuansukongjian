<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * 用户登录操作model
 * @使用表 cms_account
 */
class operation_subsidiaryModel extends BaseModel{

	/**
	 * 查找备注信息列表
	 * @param   array           当前用户id
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-19
	 */
	public function showList($where){
		$showList = $this->selectData('',$where);
		return $showList;
	}

	/**
	 * 添加备注
	 * @param   array           当前用户id，备注信息
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-8-19
	 */
	public function addRemark($add){
		$isSuccess = $this->addData($add);
		return $isSuccess;
	}

	/**
	 * 删除数据
	 * @param   array           当前用户id，备注信息
	 * @return	boolean			操作是否成功
	 * @return  msg             提示信息
	 * @author  zhanganran
	 * @time    2016-09-05
	 */
	public function	delData($where){
		$isSuccess = $this->deleteData($where);
		return $isSuccess;
	}

}
