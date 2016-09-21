<?php
/*************************************************
 * file description
 * @filename:           EditController.class.php
 * @desc:               空间申请编辑
 * @tables:             [room_info]
 * @date:               2016-8-28
 * @author:             大业
 * @version:            v1.0
 *************************************************/
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class EditController extends HomeBaseController{
    /**
     * [获取要修改表单数据]
     * @method editList
     * @author 大业
     * @create 2016-08-25
     * @return [type] [description]
     */
    public function editForm()
    {
        $uid =$this->uid;
        $rid = I('rid');
        //省信息
        $AreaProv= D('AreasInfo')->getAreaName('中国',0);

        //空间图片信息
        $SlidePic= D('SlidePicture')->getSlidePng($rid);

        //列表信息
        $Info = D('RoomInfo')->showInfo($uid,$rid,'');

        //特色标签列表
        //父列表
        $Ptags= D('AdvantageInfo')->getAdvantageInfo();
        //子列表
        foreach ($Info['list'][0]['advantage'] as $index => $tag) {
            $Stags=$tag['id'].','.$Stags;
        }
        $Stags=trim($Stags,',');
        $name="modifyFormRid".$uid;
        cookie($name,$rid,array('expire'=>3600,'prefix'=>''));
        $this->assign('AreaProv',$AreaProv);
        $this->assign('SlidePic',$SlidePic);
        $this->assign('Ptags'   ,$Ptags);
        $this->assign('Stags'   ,$Stags);
        //dump($Ptags);dump($Stags);
        $this->assign('list'    ,$Info['list'][0]);
        $this->display('Form/editRoom');
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
    	$cookie['url']=U("home/edit/editForm/id/".I('id'));
    	$cookie['type']="2";
        if(empty(I('id'))){
        	$cookie['code']="201";
        	$cookie['con']="1";
        	$cookie['msg']="提交失败";
        	$cookie['error']="空间ID不能为空";
        	cookie('yskjM',$cookie,array('expire'=>3600,'prefix'=>''));
            ajax_return('空间ID不能为空',"校验错误",999);
        }
        $rid = I('id');
        //查询条件
        $where = 'id='.$rid;
        //处理标签列表
        $advantage = implode(',',I('advantage'));
        //修改数据
        $data = array(
            'room_name'             => I('name'), //空间名
            'room_desc'             => I('desc'), //空间描述
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
            /*modify by 大业|20160905|只要有编加操作，状态全部变为初始0*/
            'is_pass'               => 0,            //通过状态：0-申请中 1-申请通过 2-申请失败
        );
        //表单校验
        $CheckRes = $this->verifyRoomForm($data);
        if($CheckRes['code'] != 200){
        	$cookie['code']="201";
        	$cookie['con']="1";
        	$cookie['msg']="提交失败";
        	$cookie['error']=$CheckRes['msg'];
        	cookie('yskjM',$cookie,array('expire'=>3600,'prefix'=>''));
        	ajax_return($CheckRes['msg'],"表单校验失败",$CheckRes['code']);
        }
        //空间头像处理[仅传路径时,不删除原图]
        $PicData = array('path'=> "Upload/Home/logo/");
        $LogoFile = $this->uploadPic($PicData);
        //如果未上传logo图片,则不对其进行处理
        if(empty($LogoFile)){
            $data['room_logo']=I('old_logo');
        }else{
        	$data['room_logo']=$LogoFile;
        }
        //添加表单数据
       $Res = D('RoomInfo')->editData($where,$data);
       if($Res === false){
	       	$cookie['code']="201";
	       	$cookie['con']="2";
	       	$cookie['msg']="修改失败";
	       	$cookie['error']="没找到要修改的信息";
	       	cookie('yskjM',$cookie,array('expire'=>3600,'prefix'=>''));
	       	ajax_return($Res,"修改失败",1);
       }else{
	       	if(!empty($LogoFile)){
	       		$path=__ROOT__.I('old_logo');
	       		@unlink($path);
	       	}
	       	$cookie['url']="http://www.yuansuzhouqi.cn/usecenter.html";
	       	$cookie['code']="200";
	       	$cookie['con']="2";
	       	$cookie['msg']="修改成功";
	       	cookie('yskjM',$cookie,array('expire'=>3600,'prefix'=>''));
	       	ajax_return($Res,"修改成功",0);
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
        //校验空间头像
        if(isset($data['room_logo']) && empty($data['room_logo'])){
            return array('code' => 11,'msg' => "空间Logo不能为空");
        }

        //校验空间名称
        $CheckRes = checkRule('name',$data['room_name']);
        if($CheckRes['code'] != 200){
            return array('code' => $CheckRes['code'],'msg' => "空间名称".$CheckRes['msg']);
        }

        //校验手机号
        $CheckRes = checkRule('mobile',$data['room_mobile']);
        if($CheckRes['code'] != 200){
            return array('code' => $CheckRes['code'],'msg' => $CheckRes['msg']);
        }

        //校验每工位金额
        $CheckRes = checkRule('money',$data['room_price_station']);
        if($CheckRes['code'] != 200){
            return array('code' => $CheckRes['code'],'msg' => $CheckRes['msg']);
        }

        //校验每平米金额
        $CheckRes = checkRule('money',$data['room_price_square']);
        if($CheckRes['code'] != 200){
            return array('code' => $CheckRes['code'],'msg' => $CheckRes['msg']);
        }

        return array('code' => $CheckRes['code'],'msg' => $CheckRes['msg']);
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
        $uid= empty($this->uid)?'0':$this->uid;
        $PicData = array(
            'path'      => "/Upload/Home/slide/",
        );
        $PicFile = $this->uploadPic($PicData);

        $rid = cookie('modifyFormRid'.$uid);
        //$rid = $_REQUEST['rid'];
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
        $rid = I('id');
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
        /*解决AJAX跨域问题*/
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $rid = I('id');
        if(empty($rid)){
            ajax_return('空间ID不能为空',"删除失败",999);
        }
        $where=' id = '.$rid;
        if(explode(',', $rid)){
            $where=' id in ('.trim($rid,',').')';
        }
        $data= array('is_del' => 1);
        $Res = D('RoomInfo')->editData($where,$data);
        if($Res){
            ajax_return($Res,"删除成功",0);
        }else{
           ajax_return($Res,"删除失败",1);
        }
    }

}
