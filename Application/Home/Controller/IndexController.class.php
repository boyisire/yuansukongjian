<?php
/*************************************************
 * file description
 * @filename:           IndexController.class.php
 * @desc:               元素空间 -- 主页
 * @tables:             [room_info;]
 * @date:               2016-8-16
 * @author:             大业
 * @version:            v1.0
 *************************************************/
namespace Home\Controller;
use Common\Controller\HomeBaseController;
class IndexController extends HomeBaseController{

    /**
     * [首页列表展示页]
     * @method index
     * @author 大业
     * @create 2016-08-17
     * @return [type] [description]
     */
	public function index()
    {
		/*	$redis = new \Redis();
			$redis->connect(C('REDIS_HOST'), C('REDIS_PORT'));
			$redis->auth(C('REDIS_AUTH'));
			$arr=array(
					'ip'=>'111.111.111.11',
					'phone'=>'13522991311',
			);
			$arr1=array(
					'ip'=>'222.222.222.22',
					'phone'=>'13522991311',
			);
			$redis->set('votes',$arr1);
			$redis->set('votes',$arr);
			var_dump($redis->get("votes"));*/

        //dump(querySMS(3029034,array('大业','天地果实')));
       // $Uid = $this->uid;
        $Uid = '';//$this->uid;
        $Rid='';
        $AjaxFlag=I('ajax');
        $AjaxInfo=I('prov')."||".I('city')."||".I('dist')."||".I('advantage;');
        //查询条件处理
        $SeachInfo=array(
            'province'  => I('prov'),//省
            'city'      => I('city'),//市
            'district'  => I('dist'),//县
            'advantage' => I('tags'),//标签
        );

        //省信息
        $AreaProv= D('AreasInfo')->getAreaName('中国',0);

        $SeachBox = $this->fmtSerach($SeachInfo);
        //主页默认显示申请通过的列表.
        $SeachBox = $SeachBox." and is_pass = 1 ";
        $ShowInfo=D('RoomInfo')->showInfo('',$Rid,$SeachBox);
        //p($ShowInfo);
        if(!empty($ShowInfo['list'])){
        	foreach($ShowInfo['list'] as $key=>$val){
        		if(empty($val['logo'])){
        			$ShowInfo['list'][$key]['logo']="/Upload/Home/logo/kongjian.png";
        		}
        	}
        }
        //选择标签
        $AdvantageList= D('AdvantageInfo')->getAdvantageInfo();
        if($AjaxFlag==1){
            ajax_return($ShowInfo,'AJAX操作',0);
        }else{
            $this->assign('AreaProv',$AreaProv);
            $this->assign('list',$ShowInfo['list']);
            $this->assign('page',$ShowInfo['page']);
            $this->assign('tags',$AdvantageList);
            $this->display();
        }
    }


    /**
     * [显示空间详情]
     * @method details
     * @author 大业
     * @create 2016-08-17
     * @return [type] [description]
     */
    public function details()
    {
        $uid = $this->uid;
        $rid = I('id');
        $PngInfo =D('SlidePicture')->getSlidePng($rid);
        $ShowInfo=D('RoomInfo')->showInfo('',$rid)['list'][0];
        //判断UID是否为空
        if(!empty($uid)){
            $CyzStatus=D('entry_check')->getStatus($uid,$rid);
            $condition = 'user_id = '.$uid.' and room_id = '.$rid;
            $CyzId = D('entry_check')->getFindInfo($condition,'id');
        }else{
            $CyzStatus='';
            $CyzId='999999999';
        }

        //p($ShowInfo);
        $this->assign('list',$ShowInfo);
        $this->assign('pngs',$PngInfo);
        $this->assign('CyzStatus',$CyzStatus);
        $this->assign('CyzId',$CyzId);
        $this->display('detail');
    }


    /**
     * [格式化筛选条件]
     * @method fmtSerach
     * @author 大业
     * @create 2016-08-17
     * @param  [array] $SeachInfo [要筛选的内容]
     * @return [string] [查询条件语句]
     */
    private function fmtSerach($SeachInfo)
    {
        $SeachBox='1=1';
        //空间位置-省
        if(!empty($SeachInfo['province'])){
            $SeachBox=$SeachBox." and room_location_province = '".$SeachInfo['province']."'";
        }
        //空间位置-市
        if(!empty($SeachInfo['city'])){
            $SeachBox=$SeachBox." and room_location_city = '".$SeachInfo['city']."'";
        }
        //空间位置-县
        if(!empty($SeachInfo['district'])){
            $SeachBox=$SeachBox." and room_location_district = '".$SeachInfo['district']."'";
        }
        //标签信息
        if(!empty($SeachInfo['advantage'])){
            foreach (explode(',', trim($SeachInfo['advantage'],',')) as $key => $value) {
                $SeachBox=$SeachBox." and find_in_set('".$value."',room_advantage)";
            }
        }
        /*if($SeachBox === '1=1'){
            return null;
        }*/
        return $SeachBox;
    }


    /**
     * [获取三级联动地图信息]
     * @method getAreaInfo
     * @author 大业
     * @create 2016-08-18
     * @param  string $name [地区名称]
     * @param  int $type [地区类型]
     * @param  int $mark [是否AJAX]
     * @return [type] [description]
     */
    public function getAreaInfo($name='北京',$type=1,$mark)
    {
        //以ajax方式查询
        if($mark ==1){
            $name = I('name');
            $type = I('type');
            $AreaList= D('AreasInfo')->getAreaName($name,$type);
            if($AreaList){
                ajax_return($AreaList,'查询成功',0);
            }else{
                ajax_return($AreaList,'查询失败',1);
            }
        }else{
            //选择位置(地名,类型:0-国 1-省 2-市 3-区)
            $AreaList= D('AreasInfo')->getAreaName($name,$type);
            return $AreaList;
        }
    }

    /**
     *
     */
    public function sendSMS(){
        $id = I('rid');
        $status = I('status');
        $rid = D('entry_check')->getFind($id);
        //取得空间信息[空间名,空间联系人,联系方式]
        $info  = D('RoomInfo')->showInfo('',$rid['room_id'],'','common')['list'][0];
        $phone = $rid['company_phone'];
        if($status == 1){
            $templ = C('SMS_TEMPLATE')['KJZ_Apply_Succ'];
        }else{
            //查找拒绝原因
            $content = empty(I('content'))?"不符合孵化器要求":I('content');
            $templ = C('SMS_TEMPLATE')['KJZ_Apply_Erro'];
        }
        $value = array($info['name'],$content);
        //手机号+空间名称+模版号
        $sendRes = sendSMS($phone,$value,$templ);
        if($sendRes['code'] == 200){
            //查询发送的短信内容:模版号+传入值
            $smsDesc = querySMS($templ,$value);
            $smsData = array(
                'sms_desc'   => $smsDesc,
                'sms_status' => $status,
                'sms_type'   => 3, //操作类型：1-验证码 2-系统消息 3-审核消息
                'accept_user'=> $info['user_id'],
                'send_time'  => time(),
            );
            //写入短信记录表
            D('Sms_info')->addData($smsData);
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * [下载文件]
     * @method downUserFile
     * @author 安然
     * @create 2016-09-01
     * @return [type] [description]
     */
    public function downUserFile(){
        $id= I('id');
        $info = D('entry_check')->getFindInfo('id='.$id,'company_prospectus');
        $infoA = D('entry_check')->getFindInfo('id='.$id,'add_time');
        $Name = explode('.',$info);
        $downName = $Name[0].'_'.$infoA.'.'.$Name[1];
        $isDown = downFile($downName,$info);
    }

    public function curlMessage($url){
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt( $ch, CURLOPT_HEADER, 0 );
       curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
       $return = curl_exec( $ch );
       curl_close( $ch );
       return $return;
   }

    public function listurl(){
      $chuangyebang=$this->curlMessage('http://www.angelgoing.com/space/index/index/p/1.html');var_dump($chuangyebang);
        //<a href="/space/index/details/id/959.html"  class="content_info" target="_blank" >
        $rul1='/<a\s*href\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^"\'>\s]+))\s*class="content_info"\s*target="_blank"\s*>/';
        preg_match_all($rul1, $chuangyebang, $chuangyebang_url);
        // $rul2 = '/<div\s*class="content_info_sizebox_title">\s*.*<\/div>/';
        //$rul2 = '/<div\s*class="content_info_sizebox_title"\s*>\s*.*\s*>/';
        $rul2='/<div\s*class="content_info_sizebox_title">\s.*?\s<\/div>/';
        preg_match_all($rul2, $chuangyebang, $chuangyebang_title);
        //P($chuangyebang_url[1]);
        //P($chuangyebang_title);
        var_dump($chuangyebang_title);
        $rul3='/>\s.*\s</';
        foreach ($chuangyebang_title as $key => $value) {
          //$res=explode(' ',$value[0]);
          preg_match_all($rul3, $value[0], $res);
          var_dump($res);
        }



    }
}
