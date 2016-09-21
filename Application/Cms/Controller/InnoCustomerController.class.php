<?php
/*************************************************
 * file description
 * @filename:           InnoCustomerController.class.php
 * @desc:               后台-创客中国管理页
 * @tables:             [kj_Innovate_Customer]
 * @date:               2016-9-09
 * @author:             大业
 * @version:            v1.0
 *************************************************/
namespace Cms\Controller;
use Common\Controller\CmsBaseController;


class InnoCustomerController extends CmsBaseController{

    /**
     * [创客中国管理页]
     * @method index
     * @author 大业
     * @create 2016-09-09
     * @return [type] [description]
     */
	public function index()
    {
        //$uid=6;
        $id = I('id');
        $search = '';
        if(I('is_pass') === '0'){
            $search = $search.'is_pass = 0 ';
        }else if(I('is_pass') === '1'){
            $search = $search.'is_pass != 0 ';
        }
        $cntNum=D('InnovateCustomer')->getCountNum($where);
        $Info  = D('InnovateCustomer')->showInfo($id,$aid,$uid,$search);
        $this->assign('cntNum'  ,$cntNum);
        $this->assign('list'    ,$Info['list']);
        $this->assign('page'    ,$Info['page']);
        $this->display('InnoCustomer/index');
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
        $id = I('id');
        if(empty($id)){
          $this->error('ID传入有误!',U('InnoCustomer/index'));
          exit;
        }

        $Info = D('InnovateCustomer')->showInfo($id,'',$uid=null,$search,$style);
        var_dump($Info);//exit;
        //行业信息
        $where = ' is_use = 0 ';
        $industry = D('IndustryInfo')->showInfoName($where);

        //融资阶段说明
        $financing = D('FinancingInfo')->showInfoName($where);

        //省信息
        $area_prov = D('AreasInfo')->getAreaName('中国',0);

        $this->assign('list'        ,$Info['list'][0]);
        $this->assign('page'        ,$Info['page']);
        $this->assign('area_prov'   ,$area_prov);
        $this->assign('industry'    ,$industry);
        $this->assign('financing'   ,$financing);

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
            $this->error('ID不能为空',U('InnoCustomer/index'));
        }

        //查询条件
        $where = 'id='.I('id');

        //修改数据
        $data = array(
            'ic_company_name'           => I('name'),       //公司名称
            'ic_company_desc'           => I('desc'),       //公司描述
            'ic_position_prov'          => I('prov'),       //位置-省
            'ic_position_city'          => I('city'),       //位置-市
            'ic_position_dist'          => I('dist'),       //位置-区
            'ic_subordinate_industry'   => I('industry'),   //所属行业
            'ic_product_link'           => I('product'),    //产品链接
            'ic_financing_stage'        => I('financing'),  //融资阶段
            'ic_principal'              => I('principal'),  //负责人
            'ic_mobile'                 => I('mobile'),     //手机号
            //'ic_prospectus'             => I('prospectus'), //企划书
       );

       //上传文件校验
       $CheckRes = $this->verifyForm($_FILES,1);
       if($CheckRes['code'] != 200){
           if($CheckRes['code'] == 300){
               //如果是手机版，则改为发送BP到邮箱.
               $data['ic_prospectus'] = $CheckRes['file'];
           }else{
               $CheckRes['aid'] = $data['aid'];
              //  $this->error($CheckRes['msg'],U('InnoCustomer/index'));
              //  exit;
           }
       }else{
           //上传路径
           $path  = "MakerChina/";
           //原始文件名
           //$upFile= explode(".",$CheckRes['file'])[0];
           $upFile = substr($CheckRes['file'],0,strrpos($CheckRes['file'],'.'));
           //转换后需上传的文件名
           $file  = urlencode($upFile).'_'.$time;
           //返回上传后全路径 [ 路径 | 文件名 | 上传类型 | 上传大小 ]
           $res  = post_upload($path,$file,$CheckRes['format'],$CheckRes['maxSize']);
           if($res['name']){
               $upDir = ltrim(dirname($res['name']),'/');
               $tmpNm = basename($res['name']);
               $upExt = substr($tmpNm,strrpos($tmpNm,'.')+1);
               $data['ic_prospectus'] = $upDir.'/'.$upFile.'.'.$upExt;
           }else{
               $CheckRes['aid'] = $data['aid'];
               $CheckRes['code']= 401;
               $CheckRes['msg'] = $res['error_info'];
              //  $this->error($CheckRes['msg'],U('InnoCustomer/index'));
              //  exit;
           }
       }

       //表单验证
       $CheckRes = $this->verifyForm($data);
       if($CheckRes['code'] != 200){
           $CheckRes['aid'] = $data['aid'];
           $this->error($CheckRes['msg'],U('InnoCustomer/index'));
           exit;
       }else{
           $res = D('InnovateCustomer')->editData($where,$data);
           if($res){
               $data['code'] = 200;
               $data['msg']  ="数据修改成功";
               $this->success($data['msg'],U('InnoCustomer/index'));
           }else{
               $data['code'] = 201;
               $data['msg']  ="数据修改失败";
               $this->error($data['msg'],U('InnoCustomer/index'));
           }
           exit;
       }

    }


    /**
     * [验证表单信息]
     * @method verifyForm
     * @author 大业
     * @create 2016-09-08
     * @param  [array] $data [验证的数据]
     * @param  [array] $flag [是否通过规则函数校验:0/1]
     * @return [boole] [true/false]
     */
    private function verifyForm($data,$flag=0)
    {
        if($flag==0){
            //校验公司名称
            $CheckRes = checkRule('name',$data['ic_company_name']);
            if($CheckRes['code'] != 200){
                return array('code' => $CheckRes['code'],'msg' => "企业名称".$CheckRes['msg']);
            }

            //校验公司描述
            $CheckRes = checkRule('name',$data['ic_company_desc'],100);
            if($CheckRes['code'] != 200){
                return array('code' => $CheckRes['code'],'msg' => "公司描述".$CheckRes['msg']);
            }

            //校验负责人
            $CheckRes = checkRule('name',$data['ic_principal']);
            if($CheckRes['code'] != 200){
                return array('code' => $CheckRes['code'],'msg' => "负责人".$CheckRes['msg']);
            }

            //校验手机号
            $CheckRes = checkRule('mobile',$data['ic_mobile']);
            if($CheckRes['code'] != 200){
                return array('code' => $CheckRes['code'],'msg' => $CheckRes['msg']);
            }
            }else{
                //校验企划书
                if(isMobile()){
                    //手机访问时，判断上传
                    return array(
                        'code'  => 300,
                        'msg'   => '手机版不需要判断文件上传',
                        'file'  => 'bp@yuansuzhouqi.cn',
                    );
                }else{
                    $format  = "docs"; //默认允许的上传文件类型(详情查看function)
                    $maxSize ='52428800';//允许默认上传大小
                    return array(
                        'code'  => 200,
                        'msg'   => '校验通过',
                        'file'  => $data['prospectus']['name'],
                        'format'=> $format,
                        'maxSize'=>$maxSize,
                    );

                }
            }

        return array('code' => $CheckRes['code'],'msg' => $CheckRes['msg']);
    }


    /**
     * [状态修改页面]
     * @method FormStatus
     * @author 大业
     * @create 2016-09-10
     */
    public function FormStatus()
    {
        $data['id']    = I('id');
        $data['status']= I('status');
        $this->assign('data',$data);
        $this->display('status');
    }


    /**
     * [接受并修改状态]
     * @method ModifyStatus
     * @author 大业
     * @create 2016-08-24
     */
    public function ModifyStatus()
    {
        $recData['id']      = I('id');
        $recData['status']  = I('status');
        $recData['desc']    = I('desc');

        if(empty($recData['id'])){
            $this->error('传入参数有误!',U('InnoCustomer/index'));
            exit;
        }

        //修改状态
        $where  = 'id = '.$recData['id'];
        $upData = array('is_pass' => $recData['status']);
        $res = D('InnovateCustomer')->editData($where,$upData);
        if($res !==false){
            //登记操作信息
            $status = 3;
            if($recData['status'] == 2){
                $status = 4;//未通过
            }
            $data = array(
                'type'      => 3, //类型：1-空间操作 2-入住操作 3-创客中国
                'r_id'      => $recData['id'],
                'op_type'   => $status,//3-通过 4-未通过
                'op_desc'   => '通过备注:'.$recData['desc'],
                'add_time'  => time(),
            );
            D('operation_subsidiary')->addData($data);

            //发送短信并登记
            $smsRes = $this->recordSMS($recData);
            //dump($smsRes);exit;
            if($smsRes){
                $this->error('短信发送成功！',U('InnoCustomer/index'));
            }else{
                $this->error('短信发送失败！',U('InnoCustomer/index'));
            }
        }else{
            $this->error('通过状态修改错误!',U('InnoCustomer/index'));
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
        $id     = $recData['id'];    //ID
        $status = $recData['status'];//状态
        //拒绝原因
        $reason = empty($recData['desc'])?'不符合创客要求':$recData['desc'];
        //取得信息企业名,联系人,联系方式]
        //showInfo($id,$aid,$uid=null,$search,$style)
        $info  = D('InnovateCustomer')->showInfo($id,'','','',$style='common')['list'][0];
        $phone = $info['mobile'];
        $value = array($info['name']);
        $templ = C('SMS_TEMPLATE')['CYJM_Apply_Succ'];
        //如果状态为2时,说明申核未通过
        if($status == 2){
             $value = array($info['name'],$reason);
             $templ = C('SMS_TEMPLATE')['CYJM_Apply_Erro'];
        }
        //手机号+名称+模版号
        $sendRes = sendSMS($phone,$value,$templ);
        //dump($sendRes);
        if($sendRes['code'] == 200){
            //查询发送的短信内容:模版号+传入值
            $smsDesc = querySMS($templ,$value);
            $smsData = array(
                'sms_desc'   => $smsDesc,
                'sms_status' => $status,
                'sms_type'   => 3, //操作类型：1-验证码 2-系统消息 3-审核消息
                'accept_user'=> $info['uid'],
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
     * [根据ID修改其状态为:删除]
     * @method delData
     * @author 大业
     * @create 2016-09-10
     * @return [type] [description]
     */
    public function delData()
    {
        echo "xxx";
        $id = I('id');
        if(empty($id)){return false;}
        $where=' id = '.$id;
        if(explode(',', $id)){
            $where=' id in ('.trim($id,',').')';
        }
        $data= array('is_del' => 1);
        $Res = D('InnovateCustomer')->editData($where,$data);
        if($Res){
            echo "删除成功!";
            $this->success('删除成功!',U('InnoCustomer/index'));
        }else{
            echo "删除失败!";
           $this->error('删除失败!',U('InnoCustomer/index'));
        }

    }

    /**
     * [电话联系页面]
     * @method FormStatus
     * @author 大业
     * @create 2016-09-10
     */
    public function FormContact()
    {
        $data['id']     = I('id');
        $data['contact']= I('contact');
        $this->assign('data',$data);
        $this->display('contact');
    }

    /**
     * [接受并修改联系状态]
     * @method ModifyContact
     * @author 大业
     * @create 2016-09-13
     */
    public function ModifyContact()
    {
        $recData['id']      = I('id');
        $recData['contact'] = I('contact');
        $recData['desc']    = I('desc');

        if(empty($recData['id'])){
            $this->error('传入参数有误!',U('InnoCustomer/index'));
            exit;
        }

        //修改状态
        $where  = 'id = '.$recData['id'];
        $upData = array('is_contact' => $recData['contact']);
        $res = D('InnovateCustomer')->editData($where,$upData);
        if($res !==false){
            //登记操作信息
            $status = 3;
            if($recData['contact'] == 2){
                $status = 4;//未通过
            }
            $data = array(
                'type'      => 3, //类型：1-空间操作 2-入住操作 3-创客中国
                'r_id'      => $recData['id'],
                'op_type'   => $status,//3-通过 4-未通过
                'op_desc'   => '电话联系:'.$recData['desc'],
                'add_time'  => time(),
            );
            $Res = D('operation_subsidiary')->addData($data);
            if($Res){
                $this->success('电话联系成功！',U('InnoCustomer/index'));
            }else{
                $this->error('电话联系失败！',U('InnoCustomer/index'));
            }

        }else{
            $this->error('联系状态修改错误!',U('InnoCustomer/index'));
        }

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
     * [文件下载]
     * @method downLoad
     * @author 安然
     * @create 2016-09-13
     * @param  [id] $PicData [Id]
     * @return 下载文件
     */
    public function downLoad(){
        $id= I('id');
        $info = D('InnovateCustomer')->findData('id='.$id,'ic_prospectus');
        $infoA = D('InnovateCustomer')->findData('id='.$id,'add_time');
        $Name = explode('.',$info['ic_prospectus']);
        $fileName = explode('/',$info['ic_prospectus']);
        $downName = $Name[0].'_'.$infoA['add_time'].'.'.$Name[1];
        $downNameS = explode('/',$downName);
        $encode = $downNameS[3];
        $downFile = $fileName[3];
        $encodeUrl = $downNameS[0].'/'.$downNameS[1].'/'.$downNameS[2];
        $isDown = downFileCms($encode,$encodeUrl,$downFile);
    }

}
