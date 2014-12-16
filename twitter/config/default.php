<?php
/*
 * api_key => twitterデベロッパー登録した際に得られるapiキー
 * api_secret => twitterデベロッパー登録した際に得られるapiシークレット
 * access_token => アプリ管理画面で発行される自分のアカウント用のアクセストークン
 * access_token_secret => アプリ管理画面で発行される自分のアカウント用のアクセストークンシークレット
 *
 * user_screen_name => アプリを使用するユーザーネーム
 *
 * dm_msg_on_follow => フォローバック時に送るDMの文面
 *                     ※文面にurlが含まれているとエラーになります(※短縮URLならOK)
 *                     ※|USERNAME|と入力するとフォローしてくれた相手のスクリーンネームに置き換わります
 *                     ※相手のスクリーンネームも含めて140文字を超えるとエラーになります
 *                     ※改行はそのまま改行として認識され、タブは削除されます
 *
 * follow_keyword => ツイートに含まれていたら無条件でフォローするキーワード
 *                   ※半角スペースで区切るとAND検索、'OR'で区切るとOR検索
 *                   ※キーワードは'OR'も含めて10個以内に抑えて下さい
 *
 * follow_search_count => 一度にフォローする数を指定します。
 *                        ※設定できる最大数は100ですが、
 *                          実際にフォローする数はアカウントのフォロワー数等によって制限される可能性があります。
 *
 * fav_keyword => ツイートに含まれていたらお気に入り登録するキーワード
 *                ※半角スペースで区切るとAND検索、'OR'で区切るとOR検索
 *                ※キーワードは'OR'も含めて10個以内に抑えて下さい
 *
 * fav_search_count => 1度にお気に入り登録できる数を指定します。
 * 					  ※設定できる最大数は100ですが、この数はAPIにより制限される可能性があります。
 *
 * search_lang => ツイートの検索対象言語をISO 639-1コードで指定
 *
 * unfollow => 何日間フォロー返ししてくれなかったらフォロー解除するかを日にちで指定
 */

return array(
	'api_key' => '',
	'api_secret' => '',
	'access_token' =>  '',
	'access_token_secret' => '',

	'user_screen_name' => '',
	'search_lang' => '',

	'follow_keyword' => '',
	'follow_search_count' => 50,

	'fav_keyword' => '',
	'fav_search_count' => 20,

	'day_to_unfollow' => 2,

	'dm_msg_on_follow' => 'Thank you for following, |USERNAME|.',

);
