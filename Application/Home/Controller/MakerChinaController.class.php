<?php
/*************************************************
 * file description
 * @filename:           makerChinaController.class.php(原控制器名:Customer)
 * @desc:               创客中国节目信息登记
 * @tables:             [kj_innovate_customer;]
 * @date:               2016-9-8
 * @author:             大业
 * @version:            v1.0
 *************************************************/
namespace Home\Controller;
use Common\Controller\HomeBaseController;
class MakerChinaController extends HomeBaseController{

    /**
     * [创客中国节目主页]
     * @method index
     * @author 大业
     * @create 2016-09-08
     * @return [type] [description]
     */
    public function index()
    {
        //环境配置
        $EnvCfg['title'] = '创客中国';
        //获取创客中国活动信息
        $where = "act_name like '%".$EnvCfg['title']."%'";
        $activeInfo = D('Activity')->findData($where);
        //需要取活动信息表中的数据:活动名称，活动ID,开始和结束时间
        $EnvCfg['aid'] = $activeInfo['id'];
        $EnvCfg['name'] = $activeInfo['act_name'];
        $time = time();
        if(($time >= $activeInfo['start_time']) && ($time <= $activeInfo['end_time'] )){
            $EnvCfg['showFlag'] = '1';
        }else{
            $EnvCfg['showFlag'] = '0';
        }
        //dump($EnvCfg);exit;
        $this->assign('EnvCfg'      ,$EnvCfg);
        $this->assign('activeInfo'  ,$activeInfo);
        //判断是否为手机
        if(isMobile()){
            $this->display('indexPhone');
        }else{
            $this->display('indexPc');
        }

    }


    /**
     * [创客中国节目表单登记页]
     * @method index
     * @author 大业
     * @create 2016-09-08
     * @return [type] [description]
     */
    public function register()
    {
        //环境配置
        $EnvCfg['title']= '创客中国';
        $EnvCfg['aid']  = I('aid');

        //省信息
        $area_prov = D('AreasInfo')->getAreaName('中国',0);

        //行业信息
        $where = ' is_use = 0 ';
        $industry = D('IndustryInfo')->showInfoName($where);

        //融资阶段说明
        $financing = D('FinancingInfo')->showInfoName($where);

        $this->assign('EnvCfg'      ,$EnvCfg);
        $this->assign('area_prov'   ,$area_prov);
        $this->assign('industry'    ,$industry);
        $this->assign('financing'   ,$financing);

        if(isMobile()){
            $this->display('formPhone');
        }else{
            $this->display('formPc');
        }
    }


    /**
     * [接受表单登记信息]
     * @method acceptForm
     * @author 大业
     * @create 2016-09-08
     * @return [type] [description]
     */
    public function acceptForm()
    {
        $time   = time();
        $data = array(
            'uid'                       => $this->uid,      //用户ID
            'aid'                       => I('aid'),        //活动ID
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
            'ic_prospectus'             => I('prospectus'), //企划书
            'add_time'                  => $time,           //添加时间
       );
        //var_dump($_FILES);
        //上传文件校验
        $CheckRes = $this->verifyForm($_FILES,1);
        if($CheckRes['code'] != 200){
            if($CheckRes['code'] == 300){
                //如果是手机版，则改为发送BP到邮箱.
                $data['ic_prospectus'] = $CheckRes['file'];
            }else{
                $CheckRes['aid'] = $data['aid'];
                /*$this->assign('data',$CheckRes);
                $this->display('message');
                exit;*/
                $this->message($CheckRes);
                //ajax_return($CheckRes['msg'],"文件校验失败",$CheckRes['code']);
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
                $this->message($CheckRes);
                /*$this->assign('data',$CheckRes);
                $this->display('message');
                exit;*/
            }
        }
        //var_dump($data);exit;
        //表单校验
        $CheckRes = $this->verifyForm($data);
        if($CheckRes['code'] != 200){
            $CheckRes['aid'] = $data['aid'];
            $this->message($CheckRes);
            /*$this->assign('data',$CheckRes);
            $this->display('message');exit;*/
        }else{
            $res = D('InnovateCustomer')->addData($data);
            if($res){
                $data['code'] = 200;
                $data['msg']  ="数据添加成功";
                //ajax_return($res,"数据插入成功!",0);
            }else{
                $data['code'] = 201;
                $data['msg']  ="数据添加失败";
                //ajax_return($res,"数据插入失败!",1);
            }
            $this->message($data);
            /*$this->assign('data',$data);
            $this->display('message');exit;*/
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
                    if(empty($data) || empty($data['prospectus']['name'])){
                        return array('code' => 400,'msg' => '上传文件不能为空!');
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
            }

        return array('code' => $CheckRes['code'],'msg' => $CheckRes['msg']);
    }


    /**
     * [成功失败跳转页]
     * @method message
     * @author 大业
     * @create 2016-09-08
     * @return [type] [description]
     */
    private function message($data){
        if(isMobile()){
            $this->assign('data',$data);
            $this->display('messagePhone');
        }else{
            $this->assign('data',$data);
            $this->display('message');
        }
        exit;
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

}

