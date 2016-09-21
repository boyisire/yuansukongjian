<?php
namespace Common\Controller;
use Common\Controller\BaseController;
use Common\Model\UsersModel;
use Common\Model\UsermetaModel;
/**
 * Home基类控制器
 */
class HomeBaseController extends BaseController{
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize();
		//判断当前访问的控制器，
		//如果action==index则不需要判断登录
		/*if(in_array(CONTROLLER_NAME,array('Index'))){
			//不需求登录
			$login=$this->isLogin(0);
		}else{
			//需要登录
			if(CONTROLLER_NAME == 'Activity' && in_array(ACTION_NAME,array('Index','index','actindex','actIndex','indextheme','indexTheme'))){
				$login=$this->isLogin(0);
			}else{
				$login=$this->isLogin(1);
			}
		}
		if($login){
			if($this->uid){
				$_SESSION['s_uinfo']['uid'] = $this->uid;
				$this->assign('login_uid',$this->uid);
				$this->assign('login_nick_name',$this->nick_name);
				$this->assign('login_is_login',$this->is_login);
			}
		}else{
			if(CONTROLLER_NAME == 'Activity' || CONTROLLER_NAME =='activity'){
				$url="http://kongjian.yuansuzhouqi.cn/home/activity/actindex";
			}else{
				$url="http://www.yuansuzhouqi.cn/wp-login.php?redirect_to=".urlencode('http://kongjian.yuansuzhouqi.cn/Home/Index/index');
			}
			header('Location:'.$url);
		}*/
		$this->uid="4";
		$this->nick_name="测试用户";
		$this->is_login="1";
		$this->assign('login_uid',$this->uid);
		$this->assign('login_nick_name',$this->nick_name);
		$this->assign('login_is_login',$this->is_login);
	}
	private function isLogin($perm){
		//cookie中的加密头&& $_COOKIE['TESCOKE'] == 'wcc'
		$cookie_name="ZQKJYSLIN_".md5('.yuansuzhouqi.cn');	
		if($perm){		
			if(isset($_COOKIE[$cookie_name]) && !empty($_COOKIE[$cookie_name])){
				return $this->login_cookie($_COOKIE[$cookie_name]);
			}else{
				
				return false;
			}
		}else{
			if(isset($_COOKIE[$cookie_name]) && !empty($_COOKIE[$cookie_name])){
		
				$this->login_cookie($_COOKIE[$cookie_name]);
			}
			return true;
		}
	}
	/**
	 * 处理数据
	 */
	private function login_cookie($COOKIE){
		$cookie=explode('|',$COOKIE);
		$cookie_elements['username']=$cookie[0];
		$cookie_elements['expiration']=$cookie[1];
		$cookie_elements['token']=$cookie[2];
		$cookie_elements['hmac']=$cookie[3];
		$cookie_elements['scheme']="logged_in";
		$expired=$cookie_elements['expiration']+3600;
		if($expired < time() ){
			return false;
		}
		$User = new UsersModel('users','wp_','connection');
		$user=$User->selectUserData(array('user_login'=>$cookie_elements['username']));
		if (empty($user) ) {
			return false;
			
		}else {
			$this->uid=$user['id'];
			$this->is_login="1";
			$this->nick_name=$user['display_name'];
			//$this->redirect($url);
			return true;	
			
		}	
			
		/*$pass_frag = substr($user['user_pass'], 8, 4);
		$verifier= hash( 'sha256', $cookie_elements['token'] );
		$key = $this->wp_hash( $cookie_elements['username'] . '|' . $pass_frag . '|' . $cookie_elements['expiration'] . '|' . $cookie_elements['token'], "logged_in" );
		$algo = "sha256";
		$hash = hash_hmac( $algo, $cookie_elements['username'] . '|' . $cookie_elements['expiration'] . '|' . $cookie_elements['token'], $key );
		if ( ! $this->hash_equals( $hash, $cookie_elements['hmac'] ) ) {
			return false;
			
		}else {
			$this->uid=$user['id'];
			$this->is_login="1";
			$this->nick_name=$user['user_nicename'];
			//$this->redirect($url);
			
			
		}		
		//验证cookie与表session存储
		//$verify=$this->verify( $cookie_elements['token'],$user['id'] );
		return true;*/
	}
	private function verify( $token ,$user_id) {
		$verifier = $this->hash_token( $token );
		//var_dump($verifier);
		return (bool) $this->get_session( $verifier ,$user_id);
	}
	private function get_session( $verifier ,$user_id) {
		$sessions = $this->get_sessions($verifier,$user_id);
		//var_dump($sessions);
		//echo $sessions;
		if ( isset( $sessions[ $verifier ] ) ) {
			return $sessions[ $verifier ];
		}
	
		return null;
	}
	private function get_sessions($verifier,$user_id) {
		$User = new UsermetaModel('usermeta','wp_','connection');
		$sessions = $User->get_user_meta($user_id, 'session_tokens',$verifier);
		//return $sessions;
		//var_dump($sessions);
		if ( ! is_array( $sessions ) ) {
			return array();
		}
		//return
		//$sessions = array_map( array( $this, 'prepare_session' ), $sessions );
		//return array_filter( $sessions, array( $this, 'is_still_valid' ) );
	}
	/**
	 * $token加密
	 */
	private function hash_token( $token ) {
		// If ext/hash is not present, use sha1() instead.
		if ( function_exists( 'hash' ) ) {
			return hash( 'sha256', $token );
		} else {
			return sha1( $token );
		}
	}
	/**
	 * 加密
	 */
	private function wp_hash($data, $scheme = 'auth') {
		$salt="E-djbcr[}[y!29Ng?ZM_RD#Lpl,B^Y@1#@?_kiRR7yjv[&.e1QSI/n#d9/FY:6&2tq+T;R[*^!C |&q=mu!zOD9f`V%>Ab&&+8}XQ2HiL(&>ZRQ=sr:O_;Chsr_N%b+7";
		return hash_hmac('md5', $data, $salt);
	}
	/**
	 * hash 比较
	 */
	private function hash_equals( $a, $b ) {
		$a_length = strlen( $a );
		if ( $a_length !== strlen( $b ) ) {
			return false;
		}
		$result = 0;
	
		// Do not attempt to "optimize" this.
		for ( $i = 0; $i < $a_length; $i++ ) {
			$result |= ord( $a[ $i ] ) ^ ord( $b[ $i ] );
		}
	
		return $result === 0;
	}
}

