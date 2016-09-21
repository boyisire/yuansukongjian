<?php
/*************************************************
 * file description
 * @filename:           AreasInfoModel.class.php
 * @desc:               地图位置信息表
 * @tables:             [kj_areas_info]
 * @date:               2016-8-16
 * @author:             大业
 * @version:            v1.0
 *************************************************/
namespace Common\Model;
use Common\Model\BaseModel;
class AreasInfoModel extends BaseModel{

	/**
	 * [获取地图位置信息]
	 * @method getAdvantage
	 * @author 大业
	 * @create 2016-08-16
	 * @param  [string] $ListID [查询条件]
	 * @return [array] [标签名称]
	 */
	private function getInfo($condtion)
	{
        $where = $condtion;
        $field = array(
            'area_id',  //地区id
            'parent_id',//地区父id
            'area_name',//地区名称
            'area_type',//地区类型:0:country[国],1:province[省],2:city[市],3:district[区]
        );
        //$order = 'area_id';
        return $this->selectData($field,$where,$order);
	}

    /**
     * [根据传入地名,取其下属地名]
     * @method getAreaName
     * @author 大业
     * @create 2016-08-18
     * @param  [string] $AreaName [地名]
     * @param  [int] $AreaType [地区类型]
     * @return [array] [地名列表]
     */
    public function getAreaName($AreaName,$AreaType)
    {
        if(empty($AreaName)){return null;}
        //取得当前地名的ID
        $where1 = "area_name like '".$AreaName."%' and area_type=".$AreaType;
        $Info1  = $this->getInfo($where1);
        if(empty($Info1)){return null;}

        //查该ID做为父ID时，所有的下载属地名
        $where2 = 'parent_id='.(int)$Info1[0]['area_id'];
        $Info2  = $this->getInfo($where2);
        //遍历格式化
        foreach ($Info2 as $key => $value) {
            $Info[$key] = $value['area_name'];
        }
        return $Info;
    }
}
