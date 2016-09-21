<?php
/*************************************************
 * file description
 * @filename:           FinancingInfoController.class.php
 * @desc:               行业信息表
 * @tables:             [Financing_info]
 * @date:               2016-09-09
 * @author:             大业
 * @version:            v1.0
 *************************************************/
namespace Cms\Controller;
use Common\Controller\CmsBaseController;


class FinancingInfoController extends CmsBaseController{

	public function index() {
        $showList = D('FinancingInfo')->showInfo();
				//逆转排序
				rsort($showList);
        $this->assign('showList',$showList);
        $this->display();
    }

    //启用
    public function enabled(){
        $condition = array('is_use'=>0);
        $where = "id = ".I('id');
        $info = D('FinancingInfo')->editData($where,$condition);
        if($info){
            echo 1;
        }else{
            echo 2;
        }
    }
    //禁用
    public function disable(){
        $condition = array('is_use'=>1);
        $where = "id = ".I('id');
        $info = D('FinancingInfo')->editData($where,$condition);
        if($info){
            echo 1;
        }else{
            echo 2;
        }
    }

    /**
     * [后台数据修改:包含 修改和添加]
     * @method editInfo
     * @author 安然,大业
     * @create 2016-09-21
     * @return [type]     [description]
     */
    public function editInfo(){
				$id = I('id');
				//添加数据
				if($id == 0) {
					$data=array(
						'financing_name' =>	I('name'),
						'financing_desc' =>	I('desc'),
						'sortord' 			=>	I('sort'),
						'add_time' 			=>	time(),
					);
					$info = D('FinancingInfo')->addData($data);
					if($info){
	            echo 3; //添加成功
	        }else{
	            echo 4; //添加失败
	        }
					exit;
				}

				//修改数据
        $condition = array(I('name')=>I('condition'));
        $where = "id = ".$id;
        $info = D('FinancingInfo')->editData($where,$condition);
        if($info){
            echo 1;
        }else{
            echo 2;
        }
				exit;
    }

}
