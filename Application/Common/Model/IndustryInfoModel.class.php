<?php
/*************************************************
 * file description
 * @filename:           IndustryInfoModel.class.php
 * @desc:               行业信息表
 * @tables:             [kj_Industry_info]
 * @date:               2016-9-8
 * @author:             大业
 * @version:            v1.0
 *************************************************/
namespace Common\Model;
use Common\Model\BaseModel;
class IndustryInfoModel extends BaseModel{

     /**
     * [查询表 Industry_info 信息]
     * @method getInfo
     * @author 大业
     * @create 2016-09-09
     * @param  [type] $condtion [查询条件]
     * @param  [type] $field [查询字段信息]
     * @return [type] [description]
     */
    public function getInfo($condtion,$field)
    {
        $where = $condtion;
        if(empty($field)){
            $field = array(
                'id',               //自增ID
                'industry_name',    //行业名称
                'industry_desc',    //行业描述
                'sortord',          //排序方式
                'is_use',           //是否使用：0-使用 1-禁用
                'add_time',         //添加时间
            );
        }
        $order = 'sortord';
        return $this->selectData($field,$where,$order);
    }


    /**
     * [根据查询条件显示查询信息]
     * @method showInfo
     * @author 大业
     * @create 2016-09-10
     * @param  [string] $search [搜索信息]
     * @return [array] [输出信息]
     */
    public function showInfo($search)
    {
        //查询条件(传入参数为空时,取所有未删除的数据)
        $where = '1 = 1 ';
        //查询条件
        if(!empty($search)){
            $where = $where.' and '.$search;
        }

        //取表数据
        $Info = $this->getInfo($where);
        return $Info;
    }


    /**
     * [根据查询条件显示查询名称]
     * @method showInfoName
     * @author 大业
     * @create 2016-09-10
     * @param  [string] $search [搜索信息]
     * @return [array] [输出信息]
     */
    public function showInfoName($search)
    {
        $list = $this->showInfo($search);
        foreach ($list as $key => $value) {
            $info[$key] = $value['industry_name'];
        }

        return $info;
    }

}
