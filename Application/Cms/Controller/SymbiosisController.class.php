<?php
/*************************************************
 * file description
 * @filename:           IndexController.class.php
 * @desc:               后台-空间合作控制器
 * @tables:             [room_info;wp_user]
 * @date:               2016-8-17
 * @author:             大业
 * @version:            v1.0
 *************************************************/
namespace Cms\Controller;
use Common\Controller\CmsBaseController;


class SymbiosisController extends CmsBaseController{

    /**
     * [空间合作主页]
     * @method index
     * @author 大业
     * @create 2016-08-17
     * @return [void] [description]
     */
	public function index(){
		// 分配菜单数据
        //$uid=6;
        $uid='';
        $rid='';
        $search = '';
        if(I('is_pass') === '0'){
            $search = $search.'is_pass = 0 ';
        }else if(I('is_pass') === '1'){
            $search = $search.'is_pass != 0 ';
        }
        $Info = D('RoomInfo')->showInfo($uid,$rid,$search,$style='cms');
        $cntNum=D('RoomInfo')->getCountNum($where);
        $TestInfo="wo shi yi tiao ce shi shu ju!";
        $this->assign('TestInfo',$TestInfo);
        $this->assign('cntNum'  ,$cntNum);
        $this->assign('list'    ,$Info['list']);
        $this->assign('page'    ,$Info['page']);
        $this->display('index');

    }

    /**
     * [获取要修改表单数据]
     * @method editList
     * @author 大业
     * @create 2016-08-25
     * @return [type] [description]
     */
    public function editForm()
    {
        $uid ='';
        $rid = I('id');
        //省信息
        $AreaProv= D('AreasInfo')->getAreaName('中国',0);

        //空间图片信息
        $SlidePic= D('SlidePicture')->getSlidePng($rid);

        $Info = D('RoomInfo')->showInfo($uid,$rid,$search);

        //特色标签列表
        //父列表
        $Ptags= D('AdvantageInfo')->getAdvantageInfo();
        //子列表
        foreach ($Info['list'][0]['advantage'] as $index => $tag) {
            $Stags=$tag['id'].','.$Stags;
        }
        $Stags=trim($Stags,',');

        $this->assign('AreaProv',$AreaProv);
        $this->assign('SlidePic',$SlidePic);
        $this->assign('Ptags'   ,$Ptags);
        $this->assign('Stags'   ,$Stags);
        $this->assign('list'    ,$Info['list'][0]);
        $this->display('edit');
    }


    /**
     * [修改表单数据]
     * @method modifyForm
     * @author 大业
     * @create 2016-08-25
     * @return [type] [description]
     */
    public function modifyForm()
    {
        if(empty(I('id'))){
            $this->error('空间ID不能为空',U('Symbiosis/index'));
        }

        //空间头像处理[仅传路径时,不删除原图]
        $PicData = array('path'=> "/Upload/Home/logo/");
        $LogoFile = $this->uploadPic($PicData);
        //查询条件
        $where = 'id='.I('id');
        //处理标签列表
        $advantage = implode(',',I('advantage'));
        //修改数据
        $data = array(
            'room_name'             => I('name'), //空间名
            'room_desc'             => I('desc'), //空间描述
            'room_logo'             => $LogoFile,
            'room_location_province'=> I('prov'),
            'room_location_city'    => I('city'),
            'room_location_district'=> I('dist'),
            'room_addr'             => I('addr'),
            'room_linkman'          => I('linkman'),//联系人
            'room_mobile'           => I('mobile'), //手机号
            'room_price_station'    => I('station'), //每工位价
            'room_price_square'     => I('square'),  //每平米价
            'room_privile'          => I('privile'), //优惠政策
            'room_advantage'        => $advantage,   //ID列表
        );

        //如果未上传logo图片,则不对其进行处理
        if(empty($LogoFile)){
            unset($data['room_logo']);
        }else{
            //清理旧头像
            $PicData = array('delfile' => I('old_logo'));
            $delFile = $this->uploadPic($PicData);
        }

        //表单校验
       $res =$this->verifyRoomForm($data);
       if($res){
            //添加表单数据
           $Res = D('RoomInfo')->editData($where,$data);
           if($Res){
                $this->success('表单修改成功',U('Symbiosis/index'));
           }else{
                $this->success('表单修改失败',U('Symbiosis/index'));
           }
       }
    }


    /**
     * [验证表单信息]
     * @method verifyForm
     * @author 大业
     * @create 2016-08-24
     * @param  [array] $data [验证的数据]
     * @return [boole] [true/false]
     */
    private function verifyRoomForm($data)
    {
        //校验空间名称
        $CheckRes = checkRule('name',$data['room_mobile']);
        if($CheckRes['code'] != 200){
            $this->error("空间名称".$CheckRes['msg']);
            return false;
        }

        //校验手机号
        $CheckRes = checkRule('mobile',$data['room_mobile']);
        if($CheckRes['code'] != 200){
            $this->error($CheckRes['msg']);
            return false;
        }

        //校验每工位金额
        $CheckRes = checkRule('money',$data['room_price_station']);
        if($CheckRes['code'] != 200){
            $this->error("工位金额".$CheckRes['msg']);
            return false;
        }

        //校验每平米金额
        $CheckRes = checkRule('money',$data['room_price_square']);
        if($CheckRes['code'] != 200){
            $this->error("平米金额".$CheckRes['msg']);
            return false;
        }

        return true;
    }


    /**
     * [空间状态修改页面]
     * @method RoomStatus
     * @author 大业
     * @create 2016-08-24
     */
    public function RoomStatus()
    {
        $data['uid']    = I('uid');
        $data['rid']    = I('rid');
        $data['status'] =I('status');
        $this->assign('data',$data);
        $this->display('status');
    }


    /**
     * [接受并修改空间状态]
     * @method ModifyStatus
     * @author 大业
     * @create 2016-08-24
     */
    public function ModifyStatus()
    {
        $recData['uid']    = I('uid');
        $recData['rid']    = I('rid');
        $recData['status'] = I('status');
        $recData['desc']   = I('desc');

        if(empty($recData['uid']) || empty($recData['rid'])){
            $this->error('传入参数有误!',U('Symbiosis/index'));
            exit;
        }

        //修改空间状态
        $where  = 'id = '.$recData['rid'];
        $upData = array('is_pass' => $recData['status']);
        $res = D('RoomInfo')->editData($where,$upData);
        if($res !==false){
            //登记操作信息
            $status = 3;
            if($recData['status'] == 2){
                $status = 4;//未通过
            }
            $data = array(
                'type'      => 1, //类型：1-空间操作 2-入住操作
                'r_id'      => $recData['rid'],
                'user_id'   => $recData['uid'],
                'op_type'   => $status,//3-通过 4-未通过
                'op_desc'   => $recData['desc'],
                'add_time'  => time(),
            );
            D('operation_subsidiary')->addRemark($data);

            //发送短信并登记
            $smsRes = $this->recordSMS($recData);
            //dump($smsRes);exit;
            if($smsRes){
                $this->error('短信发送成功！',U('Symbiosis/index'));
            }else{
                $this->error('短信发送失败！',U('Symbiosis/index'));
            }
        }else{
            $this->error('空间状态修改错误!',U('Symbiosis/index'));
        }

    }


    /**
     * [发送并记录短信信息]
     * @method recordSMS
     * @author 大业
     * @create 2016-08-24
     * @param  [int] $recData [短信信息：空间ID,空间状态(1-通过 2-拒绝),拒绝内容]
     * @return [boole] [成功/失败]
     */
    public function recordSMS($recData)
    {
        $rid    = $recData['rid'];  //空间ID
        $status = $recData['status'];//空间状态
        //拒绝原因
        $reason = empty($recData['desc'])?'不符合孵化器要求':$recData['desc'];
        //取得空间信息[空间名,空间联系人,联系方式]
        $info  = D('RoomInfo')->showInfo('',$rid,'','common')['list'][0];
        $phone = $info['mobile'];
        $value = array($info['name']);
        $templ = C('SMS_TEMPLATE')['KJZ_Apply_Succ'];
        //如果状态为2时,说明申核未通过
        if($status == 2){
             $value = array($info['name'],$reason);
             $templ = C('SMS_TEMPLATE')['CYZ_Apply_Erro'];
        }
        //手机号+空间名称+模版号
        $sendRes = sendSMS($phone,$value,$templ);
        //dump($sendRes);
        if($sendRes['code'] == 200){
            //查询发送的短信内容:模版号+传入值
            $smsDesc = querySMS($templ,$value);
            $smsData = array(
                'sms_desc'   => $smsDesc,
                'sms_status' => $status,
                'sms_type'   => 2, //操作类型：1-验证码 2-系统消息 3-审核消息
                'accept_user'=> $info['user_id'],
                'send_time'  => time(),
            );
            //写入短信记录表
            D('Sms_info')->addData($smsData);
            return true;
        }else{
            return false;
        }
    }


    /**
     * [根据空间ID修改其状态为:删除]
     * @method delData
     * @author 大业
     * @create 2016-08-17
     * @param  [int] $rid [空间ID]
     * @return [type] [description]
     */
    public function delData()
    {
        $rid = I('id');
        if(empty($rid)){return false;}
        $where=' id = '.$rid;
        if(explode(',', $rid)){
            $where=' id in ('.trim($rid,',').')';
        }
        $data= array('is_del' => 1);
        $Res = D('RoomInfo')->editData($where,$data);
        if($Res){
            //echo "删除成功!";
            $this->success('删除成功!',U('Symbiosis/index'));
        }else{
           $this->error('删除失败!',U('Symbiosis/index'));
        }

    }


    /**
     * [上传空间轮播图片]
     * @method addPicFile
     * @author 大业
     * @create 2016-08-26
     * @return [void] []
     */
    public function addPicFile()
    {
        $PicData = array(
            'path'      => "/Upload/Home/slide/",
        );
        $PicFile = $this->uploadPic($PicData);

        $rid = $_REQUEST['rid'];
        //echo "空间ID:[".$rid."]";
        $data = array(
            'room_id'   => $rid,
            'png_url'   => $PicFile,
            'add_time'  => time(),
        );
        D('SlidePicture')->addData($data);
    }


    /**
     * [删除空间轮播图片]
     * @method delPicFile
     * @author 大业
     * @create 2016-08-26
     * @return [array] [ajax数据返回信息]
     */
    public function delPicFile()
    {
        $id =I('id');
        $rid=I('roomid');
        $delfile=I('delfile');
        $where = array(
            'id'        =>  $id,
            'room_id'   =>  $rid,
        );
        $res=D('SlidePicture')->deleteData($where);
        if($res){
            @unlink(".".$delfile);
            ajax_return (basename($delfile),'删除成功!',0);
        }else{
            ajax_return (basename($delfile),'删除失败!',1);
        }


    }


    /**
     * [获取图片列表代码]
     * @method getSlidePicList
     * @author 大业
     * @create 2016-08-25
     * @return [type] [description]
     */
    public function getSlidePicList()
    {
        $rid=I('id');
        if(empty($rid)){
            ajax_return(null,'空间ID不能为空',9);
        }

        //获取空间轮播图片信息列表
        $PicList= D('SlidePicture')->getSlidePng($rid);
        //dump($PicList);exit;
        $html='';
        foreach ($PicList as $index => $name) {
            $delHtml = '<span class="file_del" id=file_del_"'.$index.'" title="删除" onclick="javascript:delFile(this,'.$index.');"></span>';
            $html .= '<div id="uploadList_'.$index.'" class="upload_append_list">';
            $html .= '   <div class="file_bar file_hover">';
            $html .= '       <div style="padding:5px;">';
            $html .= '       <input type="hidden" id="file_name_'.$index.'" value="'.$name.'">' ;
            $html .= '           <p class="file_name" title="'.basename($name).'">' .basename($name). "</p>";
            $html .= $delHtml;
            $html .= "       </div>";
            $html .= "   </div>";
            $html .= '   <a style="height:115px;width:140px;" href="#" class="imgBox">';
            $html .= '       <div class="uploadImg" style="width:125px;max-width:125px;max-height:105px;">';
            $html .= '           <img id="uploadImage_' . $index . '" class="upload_image" src="'.__ROOT__ . $name . '" style="width:expression(this.width > 125 ? 125px : this.width);" />';
            $html .= "       </div>";
            $html .= "   </a>";
            $html .= '   <p id="uploadProgress_' . $index . '" class="file_progress"></p>';
            $html .= '   <p id="uploadFailure_' . $index . '" class="file_failure">上传失败，请重试</p>';
            $html .= '   <p id="uploadSuccess_' . $index . '" class="file_success"></p>';
            $html .= "</div>";
        }
        ajax_return($html,'获取图片列表代码成功',0);
    }


    /**
     * [图片上传处理]
     * @method uploadPic
     * @author 大业
     * @create 2016-08-25
     * @param  [array] $PicData [头像数据]
     * @return [array] [文件名]
     */
    private function uploadPic($PicData)
    {
        /*注：仅传路径时，不删除原图;
         *    仅传原图，不传路径时，只清理原图
         *    具体情况视具体场景而定.
         */
        $FileLogo='';
        //新头像上传
        if(!empty($PicData['path'])){
            $Path= $PicData['path'];
            $FileLogo = upload_image($Path);
        }

        //旧头像删除
        if(!empty($PicData['delfile'])){
            $OldLogo = ".".$PicData['delfile'];
            @unlink($OldLogo);
        }

        return  $FileLogo[0];
    }


}
