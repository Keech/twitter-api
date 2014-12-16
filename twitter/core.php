<?php
namespace Twitter;

class Core {

	/*
	 * フォロー解除する
	 */
	public function post_friendships_destroy($user_id) {
		$params_query = array (
			'user_id' => $user_id
		);
		$request_method = 'POST';
		$request_url = 'https://api.twitter.com/1.1/friendships/destroy.json';

		return $this->send_request ( $params_query, $request_method, $request_url );
	}

	/*
	 * フォロワーの情報を取得する
	 */
	public function get_followers_list($user_screen_name){
		$params_query = array (
			'screen_name' => $user_screen_name
		);
		$request_method = 'GET';
		$request_url = 'https://api.twitter.com/1.1/followers/list.json';

		return $this->send_request ( $params_query, $request_method, $request_url );
	}

	/*
	 * ツイートIDを指定してお気に入りする
	 */
	public function post_favorite_create($tweet_id) {
		$params_query = array (
			'id' => $tweet_id
		);
		$request_method = 'POST';
		$request_url = 'https://api.twitter.com/1.1/favorites/create.json';

		return $this->send_request ($params_query, $request_method, $request_url);
	}

	/*
	 * ツイートを検索する
	 */
	public function search_tweets($keyword, $lang = null, $count = null){
		$params_query = array_filter(array (
			'q'     => $keyword,
			'lang'  => $lang,
			'count '=> $count,
		), function($val){
			return !empty($val);
		});

		$request_method = 'GET';
		$request_url = 'https://api.twitter.com/1.1/search/tweets.json';

		return $this->send_request($params_query, $request_method, $request_url);
	}

	/*
	 * フォローしている人のデータを取得する
	 */
	public function get_friends_info() {
		$params_query = array (
			'stringfy_ids' => 'true'
		);
		$request_method = 'GET';
		$request_url = 'https://api.twitter.com/1.1/friends/ids.json';

		return $this->send_request ($params_query, $request_method, $request_url);
	}

	/*
	 * ダイレクトメッセージを送る
	 */
	public function post_direct_messages_new($receiver_screen_name, $msg) {
		$text = str_replace('|USERNAME|', $receiver_screen_name, $msg);

		$params_query = array (
			'screen_name' => $receiver_screen_name,
			'text'        => $text,
		);
		$request_method = 'POST';
		$request_url = 'https://api.twitter.com/1.1/direct_messages/new.json';

		$this->send_request ( $params_query, $request_method, $request_url);
	}

	/*
	 * 特定のユーザーの情報を取得する
	 */
	public function get_users_show($user_screen_name) {
		$params_query = array (
			'screen_name' => $user_screen_name
		);
		$request_method = 'GET';
		$request_url = 'https://api.twitter.com/1.1/users/show.json';

		return  $this->send_request ( $params_query, $request_method, $request_url);
	}

	/*
	 *フォロワーのID一覧を取得する
	 */
	public function get_followers_ids($user_screen_name){
		$follower_ids = array();
		$params_query = array (
			'screen_name' => $user_screen_name,
		);
		$request_method = 'GET';
		$request_url = 'https://api.twitter.com/1.1/followers/ids.json';

		do{
			if(isset($result->next_cursor_str)){
				$params_query['cursor'] = $result->next_cursor_str;
			}

			$result = $this->send_request ( $params_query, $request_method, $request_url);

			if($result && isset($result->ids)){
				$follower_ids = array_merge($follower_ids, $result->ids);
			}

		}while($result && $result->next_cursor_str != 0);

		return $follower_ids;
	}

	/*
	 * 指定したIDのユーザーをフォローする
	 * アカウントが凍結されないように1日のフォロー上限も管理する
	 * フォローした人は記録して自動アンフォローの際に使用する
	 */
	public function post_friendships_create($user_id) {
		$params_query = array (
			'user_id' => $user_id
		);
		$request_method = 'POST';
		$request_url = 'https://api.twitter.com/1.1/friendships/create.json';
		return $this->send_request ( $params_query, $request_method, $request_url);
	}

	/*
	 * $textの内容でツイートする
	*/
	public function post_statuses_update($text){
		$params_query = array (
			'status' => $text,
		);
		$request_method = 'POST';
		$request_url = 'https://api.twitter.com/1.1/statuses/update.json';
		return $this->send_request($params_query, $request_method, $request_url);
	}

	/*
	 * 各種パラメータを処理し、実際にリクエストを行う
	 *
	 *  @attengion
	 *   200以外のステータスコードが返ってきた場合は記録する
	 */
	public function send_request($params_query, $request_method, $request_url, array $app_keys, $as_json = true){
		$api_secret = rawurlencode ($app_keys['api_secret']);
		$access_token_secret = rawurlencode ($app_keys['access_token_secret']);

		$signature_key = "{$api_secret}&{$access_token_secret}";

		$params_oauth = array (
			'oauth_consumer_key'     => $app_keys['api_key'],
			'oauth_token'            => $app_keys['access_token'],
			'oauth_nonce'            => microtime (),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => time (),
			'oauth_version'          => '1.0'
		);

		$params_merged = array_merge($params_query, $params_oauth);
		ksort ($params_merged);
		$signature_params = str_replace(array ('+','%7E'), array ('%20','~'), http_build_query($params_merged, '', '&'));
		$signature_params = rawurlencode($signature_params);

		$encoded_request_method = rawurlencode($request_method);
		$encoded_request_url = rawurlencode($request_url);

		$signature_data = "{$encoded_request_method}&{$encoded_request_url}&{$signature_params}";

		$hash = hash_hmac('sha1', $signature_data, $signature_key, TRUE);
		$signature = base64_encode($hash);
		$params_merged ['oauth_signature'] = $signature;

		$header_params = http_build_query($params_merged, '', ',');
		$tail = '?' . http_build_query($params_query, '', '&');
		$tail = str_replace('%2C', ',', $tail);

		$response = @file_get_contents($request_url. $tail, false, stream_context_create(array(
			'http' => array(
				'method' => $request_method,
				'header' => array(
					'Authorization: OAuth '.$header_params
				)
			)
		)));

		//$http_response_header[0];

		return $response ? ($as_json ? json_decode($response) : $response) : $response;
	}
}
