<?php
return array(
//*************************************附加设置***********************************
    //'SHOW_PAGE_TRACE'        => false,                           // 是否显示调试面板
    //'TAGLIB_BUILD_IN'        => 'Cx,Common\Tag\My',              // 加载自定义标签
    'LOAD_EXT_CONFIG'        => 'db',               // 加载网站设置文件
    'TMPL_PARSE_STRING'      => array(                           // 定义常用路径
        '__OSS__'            => OSS_URL,
        '__PUBLIC__'         => __ROOT__.__ROOT__.'/Public',
        '__HOME_CSS__'       => __ROOT__.trim(TMPL_PATH,'.').'Public/css/home',
        '__HOME_JS__'        => __ROOT__.trim(TMPL_PATH,'.').'Public/js/home',
        '__HOME_IMAGES__'    => __ROOT__.trim(TMPL_PATH,'.').'Public/images/home',
        '__ADMIN_CSS__'      => __ROOT__.trim(TMPL_PATH,'.').'Public/css/cms',
        '__ADMIN_JS__'       => __ROOT__.trim(TMPL_PATH,'.').'Public/js/cms',
        '__ADMIN_IMAGES__'   => __ROOT__.trim(TMPL_PATH,'.').'Public/images/cms',
        '__PUBLIC_CSS__'     => __ROOT__.trim(TMPL_PATH,'.').'Public/css',
        '__PUBLIC_JS__'      => __ROOT__.trim(TMPL_PATH,'.').'Public/js',
        '__PUBLIC_IMAGES__'  => __ROOT__.trim(TMPL_PATH,'.').'Public/images',
        '__USER_CSS__'       => __ROOT__.trim(TMPL_PATH,'.').'User/Public/css',
        '__USER_JS__'        => __ROOT__.trim(TMPL_PATH,'.').'User/Public/js',
        '__USER_IMAGES__'    => __ROOT__.trim(TMPL_PATH,'.').'User/Public/images',
        '__APP_CSS__'        => __ROOT__.trim(TMPL_PATH,'.').'App/Public/css',
        '__APP_JS__'         => __ROOT__.trim(TMPL_PATH,'.').'App/Public/js',
        '__APP_IMAGES__'     => OSS_URL.trim(TMPL_PATH,'.').'App/Public/images',
    	'__PUBLIC_JQUERY__'  => __ROOT__.trim(TMPL_PATH,'.').'Public/js/jquery/',
    	'__PUBLIC_UPLOAD__'  => __ROOT__,
    	'__PUBLIC_CULTURE__'  => __ROOT__.'/Upload/firmCultureBook/'
    ),
//***********************************修改定界符**************************************
    '__PUBLIC_CULTURE__'  =>'Upload/firmCultureBook/',
	'TMPL_L_DELIM'    =>    '<--{',
	'TMPL_R_DELIM'    =>    '}-->',
	'TAGLIB_BEGIN'=>'<{',//此处要注意，html页面在加载的一些标签<>的符号也要修改例如：include
	'TAGLIB_END'=>'}>',//此处要注意，html页面在加载的一些标签<>的符号也要修改例如：include
//***********************************URL设置**************************************
    'MODULE_ALLOW_LIST'      => array('Home','Cms','Api','User'), //允许访问列表
    'URL_HTML_SUFFIX'        => '.html',  // URL伪静态后缀设置
    'URL_CASE_INSENSITIVE'   => true,     //忽略大小写
	'URL_MODEL'              => 2,  //启用rewrite
//***********************************SESSION设置**********************************
    'SESSION_OPTIONS'        => array(
        'name'               => 'YUANSUZHOUQI',//设置session名
        'expire'             => 24*3600*15, //SESSION保存15天
        'use_trans_sid'      => 1,//跨页传递
        'use_only_cookies'   => 0,//是否只开启基于cookies的session的会话方式
    ),
//***********************************页面设置**************************************
    'TMPL_EXCEPTION_FILE'    => APP_DEBUG ? THINK_PATH.'Tpl/think_exception.tpl' : './Template/default/Home/Public/404.html',
    'TMPL_ACTION_ERROR'      => TMPL_PATH.'/Public/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'    => TMPL_PATH.'/Public/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
//***********************************邮件服务器**********************************
    'EMAIL_FROM_NAME'        => '', // 发件人
    'EMAIL_SMTP'             => '', // smtp
    'EMAIL_USERNAME'         => '', // 账号
    'EMAIL_PASSWORD'         => '', // 密码
//***********************************其他设置**********************************
    'RONG_IS_DEV'            => true,//是否是在开发中
    'RONG_DEV_APP_KEY'       => '8luwapkvu3xwl', //融云开发环境下的key       仅供测试使用
    'RONG_DEV_APP_SECRET'    => '1Aw1D7F6Td25', //融云开发环境下的SECRET     仅供测试使用
    'GEETEST_ID'             => '034b9cc862456adf05398821cefc94eb',//极验id  仅供测试使用
    'GEETEST_KEY'            => 'b7f064b9ae813699de794303f0b0e76f',//极验key 仅供测试使用
    'RONG_PRO_APP_KEY'       => '', //融云生产环境下的key
    'RONG_PRO_APP_SECRET'    => '', //融云生产环境下的SECRET
    'UMENG_IOS_APP_KEY'      => '', //友盟ios AppKey
    'UMENG_IOS_SECRET'       => '', //友盟ios App Master Secret
    'UMENG_ANDROID_APP_KEY'  => '', //友盟android AppKey
    'UMENG_ANDROID_SECRET'   => '', //友盟android App Master Secret
    'RONGLIAN_ACCOUNT_SID'   => '', //容联云通讯 主账号 accountSid
    'RONGLIAN_ACCOUNT_TOKEN' => '', //容联云通讯 主账号token accountToken
    'RONGLIAN_APPID'         => '', //容联云通讯 应用Id appid
    'RONGLIAN_TEMPLATE_ID'   => '', //容联云通讯 模板Id
    'NEED_UPLOAD_OSS'        => array( // 需要上传的目录
        '/Upload/avatar',
        '/Upload/cover',
        '/Upload/image/webuploader',
        '/Upload/video',
        ),
    'ALIPAY_CONFIG'          => array(
        'partner'            => '', // partner 从支付宝商户版个人中心获取
        'seller_email'       => '', // email 从支付宝商户版个人中心获取
        'key'                => '', // key 从支付宝商户版个人中心获取
        'sign_type'          => strtoupper(trim('MD5')), // 可选md5  和 RSA
        'input_charset'      => 'utf-8', // 编码 (固定值不用改)
        'transport'          => 'http', // 协议  (固定值不用改)
        'cacert'             => VENDOR_PATH.'Alipay/cacert.pem',  // cacert.pem存放的位置 (固定值不用改)
        'notify_url'         => 'http://wwww.yuansuzhouqi.cn/Api/Alipay/alipay_notify', // 异步接收支付状态通知的链接
        'return_url'         => 'http://wwww.yuansuzhouqi.cn/Api/Alipay/alipay_return', // 页面跳转 同步通知 页面路径 支付宝处理完请求后,当前页面自 动跳转到商户网站里指定页面的 http 路径。 (扫码支付专用)
        'show_url'           => 'http://wwww.yuansuzhouqi.cn/User/Order/index', // 商品展示网址,收银台页面上,商品展示的超链接。 (扫码支付专用)
        'private_key_path'   => '', //移动端生成的私有key文件存放于服务器的 绝对路径 如果为MD5加密方式；此项可为空 (移动支付专用)
        'public_key_path'    => '', //移动端生成的公共key文件存放于服务器的 绝对路径 如果为MD5加密方式；此项可为空 (移动支付专用)
        ),
    'WEIXINPAY_CONFIG'       => array(
        'APPID'              => '', // 微信支付APPID
        'MCHID'              => '', // 微信支付MCHID 商户收款账号
        'KEY'                => '', // 微信支付KEY
        'APPSECRET'          => '',  //公众帐号secert
        'NOTIFY_URL'         => 'http://wwww.yuansuzhouqi.cn/Api/WeixPay/notify/order_number/', // 接收支付状态的连接
        ),

//***********************************网易云信发短信**********************************
    'SMS_FLAG'      => 0,           //短信发送开发：0-开:发送,1-关:不发送(直接返200成功)
    'SEND_SMS'      => array(
        'AppKey'    =>'171efc39a26383244fafd6c0f21b0f80',
        'AppSecret' =>'84fcad389556',
    ),
    'SMS_TEMPLATE'  => array( //短信模版
        'CYZ_Apply'      => '3029034', //创业者 申请短信提示
        'KJZ_Apply_Erro' => '3032037', //创业者 申请失败短信
        'KJZ_Apply_Succ' => '3032038', //创业者 申请成功短信
        'CYZ_Apply_Erro' => '3033101', //空间主 申请失败短信
        'CYJM_Apply_Succ' => '3031160', //创业节目 申请失败短信
        'CYJM_Apply_Erro' => '3029121', //创业节目 申请失败短信
        'YJ_SMS'         => '',        //亚杰活动短信模板
    ),
//***********************************微信api**********************************
   	'WX_API'		=> array(
   		'appid'=>'wx2ccc5a41be00eade',
   		'secret'=>'b437acfb18aa86d9e058fe7ff19a399b'
   	),
//***********************************redis缓存设置**********************************
	'DATA_CACHE_PREFIX' => 'Redis_',//缓存前缀
	'DATA_CACHE_TYPE'=>'Redis',//默认动态缓存为Redis
	'REDIS_RW_SEPARATE' => true, //Redis读写分离 true 开启
	'REDIS_HOST'=>'127.0.0.1', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
	'REDIS_PORT'=>'6379',//端口号
	'REDIS_TIMEOUT'=>'300',//超时时间
	'REDIS_PERSISTENT'=>false,//是否长连接 false=短连接
	'REDIS_AUTH'=>'yszqKj@0!6',//AUTH认证密码
//***********************************sendcloud 发送邮件配置**********************************
	'SEND_EMAIL' =>array(
		'send_url'=>'http://sendcloud.sohu.com/webapi/mail.send.json',
		'fromname'=>'元素君',
		'api_user'=>'mvplizhi_yszq2016_pzNfOE',
		'api_key'=>'oZyBAZkqEkOI64q2',
		'from'=>'yuansujun@yuansuzhouqi.cn'
	),
);
