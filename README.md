# Twitter-Api #
Twitterでのユーザー対応を自動化するためのモジュール。
User_Stream APIとRest APIを使用している。

* * *

# 実装されている機能
 * ### Rest-API
    * フォロー機能
    * アンフォロー機能
    * ツイート検索
    * お気に入り登録機能
    * DM送信
    * フォローしている人の情報取得
    * フォロワーの情報取得

 * ### User_Stream API
    * 自動フォロー返し

* * *

# How To Use
* configフォルダの中にアカウントごとの設定ファイルを生成する 
（※全てのconfigファイルはdefault.phpとマージされる）  
※php 5.2.0以上で動作  
※php 5.4.0以上で動かした場合、Strict Standardsの警告が表示されることに注意  

* 呼び出し元のクラスで必要なファイルを読み込む

  ```php
require_once PathToLibrary.'/twitter/rest.php';
require_once PathToLibrary.'/twitter/user_stream.php');
  ```

* 読み込んだクラスのgetInstance()を実行する  
その際、引数に読み込むconfigファイル名称を指定する  
（※すべてのクラスはTwitter名前空間に属することに注意）  

  ```php
$twitter_rest = \Twitter\Rest::getInstance($conf_nm);
  ```

* 下記、cronなどから呼び出すときの例

  ```php
require_once 'twitter/rest.php';

$conf_nm = isset($argv[1]) ? $argv[1] : '';

$twitter = \Twitter\Rest::getInstance($conf_nm);
$twitter->auto_unfollow();
  ```

* * *

# 使用してるライブラリ
 * phirehose-master  
https://github.com/fennb/phirehose/wiki/Introduction