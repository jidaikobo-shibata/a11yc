<?php
/**
 * language
 *
 * @package    part of A11yc
 */

define('A11YC_LANG_POST_SERVICE_NAME', 'A11yc アクセシビリティ バリデーション サービス');
define('A11YC_LANG_POST_SERVICE_NAME_ABBR', 'A11yc AVS');

define('A11YC_LANG_POST_INDEX', 'バリデーション');

define('A11YC_LANG_POST_DESCRIPTION', 'ウェブアクセシビリティのチェッカーです。だれでもお使いいただけます。');

define('A11YC_LANG_POST_README', '使い方');

define('A11YC_LANG_POST_HOWTO', '<p>HTMLに対して、機械的にできるアクセシビリティのチェックを行います。<a href="%s">バリデーション</a>の<code>URL</code>に検査対象のURLを入力するか、<code>HTML Source</code>のtextareaにHTMLを貼付して、送信してください。アクセシビリティ上のチェックポイントとその解説文を表示します。</p><p>URLによるチェックの場合は、アクセシビリティのチェックの他に、画像とaltの確認を行うことができます。</p>');

define('A11YC_LANG_POST_SERVICE_NAME_TITLE', 'サービスの名称');
define('A11YC_LANG_POST_SERVICE_NAME_EXP', '「<strong>'.A11YC_LANG_POST_SERVICE_NAME.'</strong>」です。長いので、「<strong>'.A11YC_LANG_POST_SERVICE_NAME_ABBR.'</strong>」という表記も用います。');

define('A11YC_LANG_POST_CONDITION_TITLE', '利用制限');
define('A11YC_LANG_POST_CONDITION_EXP_FREE', '無料でお使いいただけます。');
define('A11YC_LANG_POST_CONDITION_EXP_10MIN', '10分で10回よりも多いpostがある場合、数分間postできなくなります。');
define('A11YC_LANG_POST_CONDITION_EXP_24H', '一つのIPアドレスから24時間で150回よりも多いpostがある場合、少しの間postできなくなります。');

define('A11YC_LANG_POST_COLLECTION_TITLE', '収集している情報');
define('A11YC_LANG_POST_COLLECTION_GOOGLE', 'Google Analyticsをつかってアクセス解析を行っています。');
define('A11YC_LANG_POST_COLLECTION_IP', 'IPアドレスによる制限のためIPアドレスとそのIPアドレスからアクセスされた時間を保存しています。');

define('A11YC_LANG_POST_VENDOR_TITLE', '開発元');
define('A11YC_LANG_POST_VENDOR_JIDAIKOBO', '有限会社時代工房');
define('A11YC_LANG_POST_VENDOR_JIDAIKOBO_TWITTER', '時代工房のTwitter');

define('A11YC_LANG_POST_TECH_TITLE', '技術情報');
define('A11YC_LANG_POST_TECH_A11YC', 'A11ycというライブラリを基礎にして開発しています。<a href="https://github.com/jidaikobo-shibata/a11yc">A11ycは、githubで入手可能</a>です。このライブラリも時代工房で作っています。');
define('A11YC_LANG_POST_TECH_A11YC_ADD', 'A11ycでは、このアクセシビリティバリデーションサービスで提供している機能のほかに、JIS X 8341-3:2016で求められている報告書や試験結果（チェックリスト）の作成機能があります。');
define('A11YC_LANG_POST_TECH_JWP_A11YC', 'WordPress用のプラグイン<a href="https://ja.wordpress.org/plugins/jwp-a11y/">jwp-a11y</a>もA11ycと同様の機能がありますので、おためしください。');
define('A11YC_LANG_POST_TECH_JWP_A11YC_ADD', 'Wordpressプラグイン版では、このA11yc AVSのバリデーション機能を投稿のたびに実行できるので、恒常的にアクセシビリティを意識してサイトを運営できるようになると思います。');

define('A11YC_LANG_POST_FEEDBACK_TITLE', 'フィードバック');
define('A11YC_LANG_POST_FEEDBACK_EXP', '機能、解説文の表現についての要望や修正箇所等ありましたら、メール（<a href="mailto:info@jidaikobo.com">info@jidaikobo.com</a>）でもTwitterでも、なんでもよいので、ご連絡ください。');

define('A11YC_LANG_POST_DONE', '検証しました。');
define('A11YC_LANG_POST_DONE_POINTS', '%s点の指摘事項があります。');
define('A11YC_LANG_POST_DONE_NOTICE_POINTS', '%s点の注意事項があります。');
define('A11YC_LANG_POST_DONE_IMAGE_LIST', '画像とaltの一覧を取得しました。リファラの設定などによっては、画像が表示されないことがあります。');

define('A11YC_LANG_POST_BEHAVIOUR', '動作');
define('A11YC_LANG_POST_DO_CHECK', 'アクセシビリティのチェックを行う');
define('A11YC_LANG_POST_SHOW_LIST_IMAGES', '画像とaltの一覧を表示する');
define('A11YC_LANG_POST_CANT_SHOW_LIST_IMAGES', 'HTMLのソースチェックでは画像とaltの一覧の表示はできません。');

define('A11YC_LANG_POST_BASIC_AUTH_EXP', '対象のページは基本認証で守られています。');

define('A11YC_LANG_POST_SOCIAL_TWEET', 'ツイートする');
define('A11YC_LANG_POST_SOCIAL_FACEBOOK', 'Facebook いいねボタン');
define('A11YC_LANG_POST_SOCIAL_HATENA', 'このエントリーをはてなブックマークに追加');
