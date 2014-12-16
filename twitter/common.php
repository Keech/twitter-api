<?php
namespace Twitter;

require_once dirname(__FILE__).'/core.php';

define('DS', DIRECTORY_SEPARATOR);
define('SS', ':');

define('__DATA_DIR__',dirname(__FILE__).DS.'data');
define('__CONFIG_DIR__', dirname(__FILE__).DS.'config');
define('DEFAULT_CONFIG_FILE_NAME', 'default');
define('FRIEND_COUNT_HISTORY_PREFIX',  'fcount-');
define('FRIEND_REQUEST_HISTORY_PREFIX','fhistory-');
define('DATE_FORMAT', 'Y-m-d');

class Common extends Core {

	protected $config = array();
	protected $limit = array();
	protected $friend_cnt = 0;

	protected $FRIEND_REQUEST_COUNT_DAY_FILE_PATH = '';
	protected $FRIEND_REQUEST_HISTORY_FILE_PATH = '';

	protected function set_config($conf_nm){
		if($conf_nm){
			$config = array_merge(
				include __CONFIG_DIR__.DS.DEFAULT_CONFIG_FILE_NAME.'.php',
				include __CONFIG_DIR__.DS.$conf_nm.'.php'
			);
		}else{
			$config = include __CONFIG_DIR__.DS.DEFAULT_CONFIG_FILE_NAME.'.php';
		}

		$this->config = $config;
	}

	protected function __construct($conf_nm){
		$this->set_config($conf_nm);
		$user_screen_name = $this->config ['user_screen_name'] ?: 'not setted';

		if($friends = $this->get_friends_info()){
			$this->friend_cnt = count($friends->ids);
		}

		$this->limit = $this->get_follow_limit($user_screen_name);

		if(!is_dir(__DATA_DIR__)){
			Util::mk_dir(__DATA_DIR__, 0770);
		}

		$this->FRIEND_REQUEST_COUNT_DAY_FILE_PATH = __DATA_DIR__.DS.FRIEND_COUNT_HISTORY_PREFIX.$user_screen_name;
		$this->FRIEND_REQUEST_HISTORY_FILE_PATH = __DATA_DIR__.DS.FRIEND_REQUEST_HISTORY_PREFIX.$user_screen_name;
	}

	/*
	 * アカウント凍結防止のために、1日のフォロー上限を算出する
	*/
	public function get_follow_limit($user_screen_name){
		$limit = array();
		$followers_count = 0;

		if($user = $this->get_users_show($user_screen_name))
			$followers_count = $user->followers_count;

		if($followers_count < 100){
			$limit_per_day = 10;
		}else if($followers_count < 1000){
			$limit_per_day = 20;
		}else if($followers_count < 10000){
			$limit_per_day = 30;
		}else{
			$limit_per_day = 50;
		}
		$limit['limit_per_day'] = $limit_per_day;

		if($followers_count < 100){
			$follower_limit = $followers_count * 10;
		}else if($followers_count < 500){
			$follower_limit = $followers_count * 1.5;
		}else if($followers_count < 2000){
			$follower_limit = $followers_count * 1.2;
		}else{
			$follower_limit = $followers_count * 1.1;
		}
		$limit['max'] = $follower_limit;

		return $limit;
	}

	public function send_request($params_query, $request_method, $request_url, $as_json = true){
		$api_keys = array(
			'api_key'             => $this->config['api_key'],
			'api_secret'          => $this->config['api_secret'],
			'access_token'        => $this->config['access_token'],
			'access_token_secret' => $this->config['access_token_secret'],
		);
		return parent::send_request($params_query, $request_method, $request_url, $api_keys, $as_json);
	}

	public function get_users_show(){
		return parent::get_users_show($this->config['user_screen_name']);
	}

	public function get_followers_ids(){
		return parent::get_followers_ids($this->config['user_screen_name']);
	}

	public function post_direct_messages_new($receiver_screen_name){
		return parent::post_direct_messages_new($receiver_screen_name, $this->config['dm_msg_on_follow']);
	}
}

class Util{
	public static function file_truncate(&$target_file){
		ftruncate($target_file, 0);
		rewind($target_file);
	}
	public static function get_file_size($file){
		$_tmp = ftell($file);
		fseek($file, 0, SEEK_END);
		$size = ftell($file);
		fseek($file, $_tmp);
		return $size;
	}
	public static function mk_dir($path, $mode = 0777)
	{
		if(!file_exists($path)){
			$result = mkdir($path, $mode, true);
			chmod($path, $mode);
		}
	}
}