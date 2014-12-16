<?php
namespace Twitter;

require_once dirname(__FILE__).'/common.php';
set_time_limit (0);

class User_Stream extends Common {

	private static $self;

	protected function __construct($conf_nm = ''){ //なぜかprivateにしたら怒られるためprotectedに設定
		parent::__construct($conf_nm);

		//シングルトンパターンを採用し、1回しかこの場所を通らないことを前提。
		define('TWITTER_CONSUMER_KEY',    $this->config['api_key']);
		define('TWITTER_CONSUMER_SECRET', $this->config['api_secret']);
		define('OAUTH_TOKEN',             $this->config['access_token']);
		define('OAUTH_SECRET',            $this->config['access_token_secret']);
	}

	public static function getInstance($conf_nm = ''){
		if(is_null(self::$self)){
			self::$self = new self($conf_nm);
		}
		return self::$self;
	}

	public function run(){
		$userstream = new \Twitter\MyUserConsumer(OAUTH_TOKEN, OAUTH_SECRET);
		$userstream->consume();
	}

	/*
	 * フォローバック
	 * @param $data Twitter Response
	 */
	public function follow_back($data){
		$data = is_string($data) ? json_decode($data) : $data;

		$event  = isset($data->event)  ? $data->event  : null;
		$source = isset($data->source) ? $data->source : null;

		if(($event == 'follow') && ($source->screen_name != $this->config ['user_screen_name'])){
			if(isset($source->following) && ($source->following == false)){
				$this->post_friendships_create($source->id);
			}
			$this->post_direct_messages_new($source->screen_name);
			echo 'Done: '.__CLASS__.': '.__FUNCTION__.PHP_EOL;
		}
	}
}

require_once dirname(__FILE__).'/lib/phirehose-master/phirehose-master/lib/UserstreamPhirehose.php';

class MyUserConsumer extends UserstreamPhirehose{
	public function enqueueStatus($status){
		$user_streamr = \Twitter\User_Stream::getInstance();
		$user_streamr->follow_back($status);
	}
}
