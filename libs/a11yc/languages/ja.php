<?php
/**
 * language
 *
 * @package    part of A11yc
 */

// general
define('A11YC_LANG_LEVEL', 'レベル');
define('A11YC_LANG_EXIST', '適用');
define('A11YC_LANG_EXIST_NON', '適用なし');
define('A11YC_LANG_PASS', '適合');
define('A11YC_LANG_CRITERION', '達成基準');
define('A11YC_LANG_HERE', 'こちら');
define('A11YC_LANG_TEST_RESULT', '試験結果');
define('A11YC_LANG_CURRENT_LEVEL', '達成しているレベル');
define('A11YC_LANG_CURRENT_LEVEL_WEBPAGES', 'サイトで達成しているレベル');
define('A11YC_LANG_NUM_OF_CHECKED', 'チェック済みのページ数');
define('A11YC_LANG_CHECKED_PAGES', 'チェック対象のページ');
define('A11YC_LANG_UNPASSED_PAGES', '目標とする達成等級に満たないページ');
define('A11YC_LANG_UNPASSED_PAGES_NO', 'すべてのチェック済みページが目標とする達成基準を満たしています');
define('A11YC_LANG_RELATED', '関連項目');
define('A11YC_LANG_AS', 'アクセシビリティ・サポーテッド');
define('A11YC_LANG_UNDERSTANDING', '解説');
define('A11YC_LANG_NO_DOC', 'ドキュメントが存在しません');

// login
define('A11YC_LANG_LOGIN_TITLE', 'A11YC ログイン');
define('A11YC_LANG_LOGIN_USERNAME', 'ユーザ名');
define('A11YC_LANG_LOGIN_PASWWORD', 'パスワード');
define('A11YC_LANG_LOGIN_BTN', 'ログインする');
define('A11YC_LANG_LOGIN_ERROR0', 'ログインできませんでした');
define('A11YC_LANG_LOGOUT', 'ログアウト');

// center
define('A11YC_LANG_CENTER_TITLE', 'センター');

// pages
define('A11YC_LANG_PAGES_TITLE', '対象ページ一覧');
define('A11YC_LANG_PAGES_PAGETITLE', '対象ページのtitle');
define('A11YC_LANG_PAGES_URLS', '対象ページのURL');
define('A11YC_LANG_PAGES_URLS_ADD', 'URLを追加する');
define('A11YC_LANG_PAGES_NOT_FOUND', '対象となるページが存在しません');
define('A11YC_LANG_PAGES_DONE', '終了');
define('A11YC_LANG_PAGES_CHECK', 'チェック');
define('A11YC_LANG_PAGES_DELETE', '削除');
define('A11YC_LANG_PAGES_UNDELETE', '復活');

// setup
define('A11YC_LANG_SETUP_TITLE', '設定');
define('A11YC_LANG_SETUP_TITLE_ETC', 'その他の設定');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR', 'チェックリストの振る舞い');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR', 'チェック時にパスした項目は消える');
define('A11YC_LANG_SETUP_SUBMIT', '送信');

define('A11YC_LANG_DECLARE_DATE', '表明日');
define('A11YC_LANG_STANDARD', '規格の種類');
define('A11YC_LANG_DEPENDENCIES', '依存したウェブコンテンツ技術のリスト');
define('A11YC_LANG_TEST_PERIOD', '試験実施期間');
define('A11YC_LANG_TARGET_LEVEL', '目標とする達成等級');
define('A11YC_LANG_POLICY', 'アクセシビリティ方針');
define('A11YC_LANG_POLICY_DESC', 'ウェブアクセシビリティ確保に取り組む理由、達成目標日、対応方針、例外事項、追加して目標とする達成等級などを書いてください。');
define('A11YC_LANG_REPORT', 'アクセシビリティ報告書');
define('A11YC_LANG_REPORT_DESC', '対象範囲、選出方法、依存したウェブコンテンツ技術などを書いてください。');
define('A11YC_LANG_CONTACT', '連絡先');
define('A11YC_LANG_CONTACT_DESC', 'アクセシビリティの不備等のため、情報を取得できなかった時の問い合わせ先、あるいはウェブアクセシビリティに関する問い合わせ先を書いてください。');

define('A11YC_LANG_CANDIDATES0', '選択した方法');
define('A11YC_LANG_CANDIDATES1', 'すべてのウェブページを選択');
define('A11YC_LANG_CANDIDATES2', 'ランダムに選択');
define('A11YC_LANG_CANDIDATES3', 'ウェブページ一式を代表するウェブページを選択');
define('A11YC_LANG_CANDIDATES4', 'ウェブページ一式を代表するウェブページとランダムに選択したウェブページとを合わせて選択');

// checklist
define('A11YC_LANG_CHECKLIST_NOT_FOUND_ERR', '自動チェックではエラーが見つかりませんでした');
define('A11YC_LANG_CHECKLIST_TITLE', 'チェックリスト');
define('A11YC_LANG_CHECKLIST_DONE', 'チェック終了');
define('A11YC_LANG_CHECKLIST_RESTOFNUM', '残りのチェック項目数');
define('A11YC_LANG_CHECKLIST_ACHIEVEMENT', '達成等級');
define('A11YC_LANG_CHECKLIST_CONFORMANCE', '%s 準拠');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL', '%s 一部準拠');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL', '目標とする達成基準以上の達成項目');

// bulk
define('A11YC_LANG_BULK_TITLE', '一括処理');
define('A11YC_LANG_BULK_UPDATE', '一括処理タイプ');
define('A11YC_LANG_BULK_UPDATE1', '新規チェック時の初期値として保存する（既存のページの試験結果を一切変更しません）');
define('A11YC_LANG_BULK_UPDATE2', 'チェックしたものだけを既存の試験結果に反映する（チェックを外す更新は行いません）');
define('A11YC_LANG_BULK_UPDATE3', 'チェックを外したものも既存の試験結果に反映する（注意：サイト内で全ページの試験結果が同じになります）');
define('A11YC_LANG_BULK_DONE', 'チェック終了');
define('A11YC_LANG_BULK_DONE1', '既存のページのチェックフラグを変更しない');
define('A11YC_LANG_BULK_DONE2', '既存のページをすべてチェック終了にする');
define('A11YC_LANG_BULK_DONE3', '既存のページをすべて未チェックにする');

// documents
define('A11YC_LANG_DOCS_TITLE', '参考資料');
define('A11YC_LANG_DOCS_TEST', '試験方法について');
