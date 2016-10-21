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
define('A11YC_LANG_JUMP_TO_CONTENT', '本文へ移動');
define('A11YC_LANG_BEGINNING_OF_THE_CONTENT', 'ここから本文です');
define('A11YC_LANG_UPDATE_SUCCEED', '更新しました');
define('A11YC_LANG_UPDATE_FAILED', '更新に失敗しました');
define('A11YC_LANG_ERROR_NON_TARGET_LEVEL', '設定で「目標とする達成等級」を選んでからチェックを行ってください。');
define('A11YC_LANG_CTRL_CONFIRM', '元には戻せません。本当に%sしてもよろしいですか？');
define('A11YC_LANG_CTRL_KEYWORD_TITLE', 'キーワード');
define('A11YC_LANG_CTRL_ORDER_TITLE', '並び替え');
define('A11YC_LANG_CTRL_SEARCH', '検索する');
define('A11YC_LANG_CTRL_SEND', '送信');
define('A11YC_LANG_CTRL_PREV', '前へ');
define('A11YC_LANG_CTRL_NEXT', '次へ');
define('A11YC_LANG_CTRL_NUM', '表示件数');
define('A11YC_LANG_CTRL_EXPAND', '展開する');
define('A11YC_LANG_CTRL_COMPRESS', '縮小する');

// login
define('A11YC_LANG_AUTH_TITLE', 'A11YC ログイン');
define('A11YC_LANG_LOGIN_USERNAME', 'ユーザ名');
define('A11YC_LANG_LOGIN_PASWWORD', 'パスワード');
define('A11YC_LANG_LOGIN_BTN', 'ログインする');
define('A11YC_LANG_LOGIN_ERROR0', 'ログインできませんでした');
define('A11YC_LANG_LOGOUT', 'ログアウト');

// center
define('A11YC_LANG_CENTER_TITLE', 'センター');
define('A11YC_LANG_CENTER_BOOKMARKLET_EXP', '以下のリンクをブラウザのブックマークに登録してください。任意のページを検査対象にできます。');
define('A11YC_LANG_CENTER_ABOUT', 'A11ycについて');
define('A11YC_LANG_CENTER_LOGO', 'ロゴマーク');
define('A11YC_LANG_CENTER_ABOUT_CONTENT', 'A11ycは、有限会社時代工房が作成したWCAG2.0対応のウェブアクセシビリティチェッカーです。');


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
define('A11YC_LANG_PAGES_PURGE', '完全に削除');
define('A11YC_LANG_PAGES_ADD_DATE', '登録日');
define('A11YC_LANG_PAGES_ORDER_ADD_DATE_ASC', '登録日昇順');
define('A11YC_LANG_PAGES_ORDER_ADD_DATE_DESC', '登録日降順');
define('A11YC_LANG_PAGES_ORDER_TEST_DATE_ASC', '試験日昇順');
define('A11YC_LANG_PAGES_ORDER_TEST_DATE_DESC', '試験日降順');
define('A11YC_LANG_PAGES_ORDER_URL_ASC', 'URL昇順');
define('A11YC_LANG_PAGES_ORDER_URL_DESC', 'URL降順');
define('A11YC_LANG_PAGES_ORDER_PAGE_NAME_ASC', 'ページ名昇順');
define('A11YC_LANG_PAGES_ORDER_PAGE_NAME_DESC', 'ページ名降順');
define('A11YC_LANG_PAGES_CTRL', '操作');
define('A11YC_LANG_PAGES_URL_FOR_EACH_LINE', '各行に一つのURLを入力して、「'.A11YC_LANG_PAGES_URLS_ADD.'」を押してください。一度の登録は10個程度にしてください。あまり多いと登録処理でプログラムが停止することがあります');
define('A11YC_LANG_PAGES_ALL', 'すべて');
define('A11YC_LANG_PAGES_YET', '未チェック');
define('A11YC_LANG_PAGES_TRASH', '削除済み');

// setup
define('A11YC_LANG_SETUP_TITLE', '設定');
define('A11YC_LANG_SETUP_TITLE_ETC', 'その他の設定');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR', 'チェックリストの振る舞い');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR', 'チェック時にパスした項目は表示しない');

define('A11YC_LANG_DECLARE_DATE', '表明日');
define('A11YC_LANG_STANDARD', '規格の種類');
define('A11YC_LANG_DEPENDENCIES', '依存したウェブコンテンツ技術のリスト');
define('A11YC_LANG_TEST_PERIOD', '試験実施期間');
define('A11YC_LANG_TEST_DATE', '試験実施日');
define('A11YC_LANG_TARGET_LEVEL', '目標とする達成等級');
define('A11YC_LANG_POLICY', 'アクセシビリティ方針');
define('A11YC_LANG_POLICY_DESC', 'ウェブアクセシビリティ確保に取り組む理由、達成目標日、対応方針、例外事項、追加して目標とする達成等級などを書いてください。');
define('A11YC_LANG_REPORT', 'アクセシビリティ報告書');
define('A11YC_LANG_REPORT_DESC', '所見等があったら、記述してください。');
define('A11YC_LANG_CONTACT', 'アクセシビリティに関する連絡先');
define('A11YC_LANG_CONTACT_DESC', 'アクセシビリティの不備等のため、情報を取得できなかった時の問い合わせ先、あるいはウェブアクセシビリティに関する問い合わせ先を書いてください。');

define('A11YC_LANG_CANDIDATES_TITLE', '選択した方法');
define('A11YC_LANG_CANDIDATES0', 'ウェブページ単位');
define('A11YC_LANG_CANDIDATES1', 'すべてのウェブページを選択');
define('A11YC_LANG_CANDIDATES2', 'ランダムに選択');
define('A11YC_LANG_CANDIDATES3', 'ウェブページ一式を代表するウェブページを選択');
define('A11YC_LANG_CANDIDATES4', 'ウェブページ一式を代表するウェブページとランダムに選択したウェブページとを合わせて選択');

// checklist
define('A11YC_LANG_CHECKLIST_TARGETPAGE', '対象ページ');
define('A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR', 'チェック対象のページを見つけられませんでした');
define('A11YC_LANG_CHECKLIST_NOT_FOUND_ERR', '自動チェックではエラーが見つかりませんでした');
define('A11YC_LANG_CHECKLIST_TITLE', 'チェックリスト');
define('A11YC_LANG_CHECKLIST_DONE', 'チェック終了');
define('A11YC_LANG_CHECKLIST_RESTOFNUM', '残りのチェック項目数');
define('A11YC_LANG_CHECKLIST_TOTAL', '計');
define('A11YC_LANG_CHECKLIST_ACHIEVEMENT', '達成等級');
define('A11YC_LANG_CHECKLIST_CONFORMANCE', '%s 準拠');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL', '%s 一部準拠');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL', '目標とする達成基準以上の達成項目');
define('A11YC_LANG_CHECKLIST_NON_INTERFERENCE', '非干渉');
define('A11YC_LANG_CHECKLIST_DO_LINK_CHECK', 'リンク切れをチェック');
define('A11YC_LANG_CHECKLIST_VIEW_SOURCE', 'ソースコードを見る');
define('A11YC_LANG_CHECKLIST_MACHINE_CHECK', '自動チェック');

// bulk
define('A11YC_LANG_BULK_TITLE', '一括処理');
define('A11YC_LANG_BULK_UPDATE', '一括処理タイプ');
define('A11YC_LANG_BULK_UPDATE1', '新規チェック時の初期値として保存する（既存のページの試験結果を一切変更しません）');
define('A11YC_LANG_BULK_UPDATE2', 'チェックしたものだけを既存の試験結果に反映する（チェックを外す更新は行いません）');
//define('A11YC_LANG_BULK_UPDATE3', 'チェックを外したものも既存の試験結果に反映する（注意：サイト内で全ページの試験結果が同じになります）');
define('A11YC_LANG_BULK_UPDATE3', 'すべての項目を既存の試験結果に反映する（注意：サイト内で全ページの試験結果が同じになります）');
define('A11YC_LANG_BULK_DONE', 'チェック終了');
define('A11YC_LANG_BULK_DONE1', '既存のページのチェックフラグを変更しない');
define('A11YC_LANG_BULK_DONE2', '既存のページをすべてチェック終了にする');
define('A11YC_LANG_BULK_DONE3', '既存のページをすべて未チェックにする');

// documents
define('A11YC_LANG_DOCS_TITLE', '参考資料');
define('A11YC_LANG_DOCS_EACH_TITLE', '参考資料');
define('A11YC_LANG_DOCS_EACH_SUBTITLE', '「%s」について');
define('A11YC_LANG_DOCS_EACH_SUBTITLE_HOWTO', '「%s」の解説');
define('A11YC_LANG_DOCS_TEST', '試験方法について');
define('A11YC_LANG_DOCS_UNDERSTANDING', '達成基準&nbsp;%s&nbsp;を理解する');
define('A11YC_LANG_DOCS_SEARCH_RESULT_NONE', '該当する資料が見つかりませんでした');
