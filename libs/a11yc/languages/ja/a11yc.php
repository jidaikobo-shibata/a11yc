<?php
/**
 * language
 *
 * @package    part of A11yc
 */

// general
define('A11YC_LANG_PRINCIPLE', '原則');
define('A11YC_LANG_GUIDELINE', 'ガイドライン');

define('A11YC_LANG_LEVEL', 'レベル');
define('A11YC_LANG_EXIST', '適用');
define('A11YC_LANG_EXIST_NON', '適用なし');
define('A11YC_LANG_PASS', '適合');
define('A11YC_LANG_CRITERION', '達成基準');
define('A11YC_LANG_HERE', 'こちら,ここ,ここをクリック,コチラ');
define('A11YC_LANG_TEST_RESULT', '試験結果');
define('A11YC_LANG_CURRENT_LEVEL', 'ページで達成しているレベル');
define('A11YC_LANG_CURRENT_LEVEL_WEBPAGES', 'サイトで達成しているレベル');
define('A11YC_LANG_NUM_OF_CHECKED', 'チェック済みのページ数');
define('A11YC_LANG_CHECKED_PAGES', 'アクセシビリティチェック対象のページ');
define('A11YC_LANG_UNPASSED_PAGES', '目標とする適合レベルに満たないページ');
define('A11YC_LANG_UNPASSED_PAGES_NO', 'すべてのチェック済みページが目標とする達成基準を満たしています');
define('A11YC_LANG_RELATED', '関連項目');
define('A11YC_LANG_AS', 'アクセシビリティ・サポーテッド');
define('A11YC_LANG_UNDERSTANDING', '解説');
define('A11YC_LANG_NO_DOC', 'ドキュメントが存在しません');
define('A11YC_LANG_JUMP_TO_CONTENT', '本文へ移動');
define('A11YC_LANG_BEGINNING_OF_THE_CONTENT', 'ここから本文です');
define('A11YC_LANG_UPDATE_SUCCEED', '更新しました');
define('A11YC_LANG_UPDATE_FAILED', '更新に失敗しました');
define('A11YC_LANG_ERROR_NON_TARGET_LEVEL', '設定で「目標とする適合レベル」を選んでください。');
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
define('A11YC_LANG_AUTH_TITLE', 'ログイン');
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
define('A11YC_LANG_PAGES_TITLE', 'チェック');
define('A11YC_LANG_PAGES_INDEX', '対象ページ一覧');
define('A11YC_LANG_PAGES_PAGETITLE', '対象ページのtitle');
define('A11YC_LANG_PAGES_URLS', '対象ページのURL');
define('A11YC_LANG_PAGES_URLS_ADD', 'URLを追加する');
define('A11YC_LANG_PAGES_NOT_FOUND', '対象となるページが存在しません');
define('A11YC_LANG_PAGES_ALREADY_EXISTS', 'すでに登録されています');
define('A11YC_LANG_PAGES_ADDED_NORMALLY', '登録しました');
define('A11YC_LANG_PAGES_ADD_FAILED', '登録に失敗しました');
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
define('A11YC_LANG_PAGES_URL_FOR_EACH_LINE', '各行に一つのURLを入力して、「'.A11YC_LANG_PAGES_URLS_ADD.'」を押してください。一度の登録は20個程度にしてください。あまり多いと登録処理でプログラムが停止することがあります');
define('A11YC_LANG_PAGES_ALL', 'すべて');
define('A11YC_LANG_PAGES_YET', 'チェック中');
define('A11YC_LANG_PAGES_TRASH', '削除済み');
define('A11YC_LANG_PAGES_GET_URLS', 'URLを取得する');
define('A11YC_LANG_PAGES_GET_URLS_EXP', '対象のページのa要素からURLの一覧を生成します。サイト構造の再帰的な走査は未対応です。');
define('A11YC_LANG_PAGES_GET_URLS_BTN', A11YC_LANG_PAGES_GET_URLS);
define('A11YC_LANG_PAGES_INDEX_INFORMATION', '%s/%d %d件〜%d件目を表示中');
define('A11YC_LANG_PAGES_DELETE_DONE', '%sを削除しました');
define('A11YC_LANG_PAGES_PURGE_DONE', '%sを完全に削除しました');
define('A11YC_LANG_PAGES_UNDELETE_DONE', '%sを復活しました');
define('A11YC_LANG_PAGES_DELETE_FAILED', '%sの削除に失敗しました');
define('A11YC_LANG_PAGES_PURGE_FAILED', '%sの完全な削除に失敗しました');
define('A11YC_LANG_PAGES_UNDELETE_FAILED', '%sの復活に失敗しました');
define('A11YC_LANG_PAGES_RETURN_TO_PAGES', 'リンクされているページの一覧を取得しました。ここをクリックして「'.A11YC_LANG_PAGES_INDEX.'」に戻って、登録をしてください。');
define('A11YC_LANG_PAGES_PRESS_ADD_BUTTON', 'リンクされているページの一覧を取得しました。一覧の内容を確認の上、「'.A11YC_LANG_PAGES_URLS_ADD.'」を押して、登録してください。');
define('A11YC_LANG_PAGES_NOT_FOUND_ALL', '有効なリンク先を見つけられませんでした。とりあえず、ここをクリックして「'.A11YC_LANG_PAGES_INDEX.'」に戻ってください。');
define('A11YC_LANG_PAGES_NOT_FOUND_SSL', '.htaccessで、httpへのアクセスをhttpsにリダイレクトしていると、リンクを見つけられないことがあります。.htaccessの<code>RewriteEngine On</code>のあと、SSL関連のリダイレクトの条件に、<code>RewriteCond %{QUERY_STRING} !a11yc=ssl</code>の一行を加えて試してみてください。');
define('A11YC_LANG_PAGES_ADD_TO_DATABASE', 'URLをデータベースに登録します');
define('A11YC_LANG_PAGES_ADD_TO_CANDIDATE', 'HTMLから候補になるURLを取得します');
define('A11YC_LANG_PAGES_IT_TAKES_TIME', 'この処理には、時間がかかります。');

// setup
define('A11YC_LANG_SETUP_TITLE', '設定');
define('A11YC_LANG_SETUP_TITLE_ETC', 'その他の設定');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR', 'チェックリストの振る舞い');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR', 'チェック時にパスした項目は表示しない');
define('A11YC_LANG_SETUP_BASIC_AUTH_TITLE', '基本認証');
define('A11YC_LANG_SETUP_BASIC_AUTH_EXP', '試験対象のサイトが基本認証で守られている場合、ここに基本認証用のユーザ名とパスワードを入力してください。');
define('A11YC_LANG_SETUP_BASIC_AUTH_USER', '基本認証ユーザ名');
define('A11YC_LANG_SETUP_BASIC_AUTH_PASS', '基本認証パスワード');
define('A11YC_LANG_SETUP_IS_USE_GUZZLE', 'Guzzleを停止');
define('A11YC_LANG_SETUP_IS_USE_GUZZLE_EXP', 'なんらかの理由でGuzzleが競合する場合、Guzzleを停止してください。Guzzleを停止しても、更新された投稿のアクセシビリティチェックは行えますが、報告書作成等の機能が失われます。できれば、原因を取り除いてください。');

define('A11YC_LANG_DECLARE_DATE', '表明日');
define('A11YC_LANG_STANDARD', '規格の種類');
define('A11YC_LANG_DEPENDENCIES', '依存したウェブコンテンツ技術のリスト');
define('A11YC_LANG_TEST_PERIOD', '試験実施期間');
define('A11YC_LANG_TEST_DATE', '試験実施日');
define('A11YC_LANG_TARGET_LEVEL', '目標とする適合レベル');
define('A11YC_LANG_POLICY', 'アクセシビリティ方針');
define('A11YC_LANG_POLICY_DESC', 'ウェブアクセシビリティ確保に取り組む理由、達成目標日、対応方針、例外事項などを書いてください。');
define('A11YC_LANG_REPORT', 'アクセシビリティ報告書');
define('A11YC_LANG_OPINION', '所見');
define('A11YC_LANG_REPORT_DESC', '所見等があったら、記述してください。');
define('A11YC_LANG_CONTACT', 'アクセシビリティに関する連絡先');
define('A11YC_LANG_CONTACT_DESC', 'アクセシビリティの不備等のため、情報を取得できなかった時の問い合わせ先、あるいはウェブアクセシビリティに関する問い合わせ先を書いてください。');

define('A11YC_LANG_CANDIDATES_TITLE', '選択した方法');
define('A11YC_LANG_CANDIDATES0', 'ウェブページ単位');
define('A11YC_LANG_CANDIDATES1', 'すべてのウェブページを選択');
define('A11YC_LANG_CANDIDATES2', 'ランダムに選択');
define('A11YC_LANG_CANDIDATES3', 'ウェブページ一式を代表するウェブページを選択');
define('A11YC_LANG_CANDIDATES4', 'ウェブページ一式を代表するウェブページとランダムに選択したウェブページとを合わせて選択');

define('A11YC_LANG_CANDIDATES_REASON', 'ページ選出理由');
define('A11YC_LANG_CANDIDATES_IMPORTANT', '代表的なページ');
define('A11YC_LANG_CANDIDATES_RANDOM', 'ランダムに選出したページ');
define('A11YC_LANG_CANDIDATES_ALL', 'すべてのページが対象のため');
define('A11YC_LANG_CANDIDATES_PAGEVIEW', 'アクセス数の多いページ');
define('A11YC_LANG_CANDIDATES_NEW', '新しいページ');
define('A11YC_LANG_CANDIDATES_ETC', 'その他の基準で選出');

// checklist
define('A11YC_LANG_CHECKLIST_TARGETPAGE', '対象ページ');
define('A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR', 'チェック対象のページを見つけられませんでした');
define('A11YC_LANG_CHECKLIST_NOT_FOUND_ERR', '自動チェックではエラーが見つかりませんでした');
define('A11YC_LANG_CHECKLIST_COULD_NOT_DRAW_HTML', 'なんらかの理由でHTMLの取得に失敗しました');
define('A11YC_LANG_CHECKLIST_TITLE', 'チェックリスト');
define('A11YC_LANG_CHECKLIST_DONE', 'チェック終了');
define('A11YC_LANG_CHECKLIST_RESTOFNUM', '残りのチェック項目数');
define('A11YC_LANG_CHECKLIST_TOTAL', '計');
define('A11YC_LANG_CHECKLIST_ACHIEVEMENT', '適合レベル');
define('A11YC_LANG_CHECKLIST_CONFORMANCE', '%s 準拠');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL', '%s 一部準拠');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL', '目標とする達成基準以上の達成項目');
define('A11YC_LANG_CHECKLIST_NON_INTERFERENCE', '非干渉');
define('A11YC_LANG_CHECKLIST_DO_LINK_CHECK', 'リンク切れをチェック');
define('A11YC_LANG_CHECKLIST_SOURCE', 'ソースコード');
define('A11YC_LANG_CHECKLIST_VIEW_SOURCE', 'ソースコードを見る');
define('A11YC_LANG_CHECKLIST_MACHINE_CHECK', '自動チェック');
define('A11YC_LANG_CHECKLIST_CHECK_RESULT', 'チェック結果');
define('A11YC_LANG_CHECKLIST_UA', 'ユーザーエージェント');
define('A11YC_LANG_CHECKLIST_REAL_URL', 'チェックしたURL');
define('A11YC_LANG_CHECKLIST_MEMO', '備考');
define('A11YC_LANG_NO_BROKEN_LINK_FOUND', 'リンク切れは見つかりませんでした');
define('A11YC_LANG_CHECKLIST_PERCENTAGE', '達成度');
define('A11YC_LANG_CHECKLIST_NG_REASON', '不適合理由');
define('A11YC_LANG_CHECKLIST_NG_REASON_EXP', 'この達成項目の対象の適用があり、かつ不適合である場合はここにその理由を記入してください。ここに理由が書いてあると、この達成基準は「不適合」あるいは「部分適合」扱いとなります。');
define('A11YC_LANG_IMAGE', '画像');
define('A11YC_LANG_IMPORTANCE', '重要度');
define('A11YC_LANG_ELEMENT', '要素');
define('A11YC_LANG_ATTRS', '属性');
define('A11YC_LANG_IMPORTANT', '重要');
define('A11YC_LANG_NEED_CHECK', '要確認');
define('A11YC_LANG_CHECKLIST_IMPORTANT_EMP', 'a要素などに含まれている要素については「重要」と表示されます。');
define('A11YC_LANG_CHECKLIST_IMPORTANT_EMP2', '「重要」な要素について、altが空であるような場合、「要確認」と表示されます。');
define('A11YC_LANG_CHECKLIST_IMPORTANT_EMP3', 'altのつけ方については、<a href="%s">1.1.1の文書</a>を参考にしてください。');
define('A11YC_LANG_CHECKLIST_ALT_NULL', 'alt属性値が存在しません');
define('A11YC_LANG_CHECKLIST_ALT_EMPTY', 'alt属性値が空です');
define('A11YC_LANG_CHECKLIST_ALT_BLANK', 'alt属性値が空白文字です');
define('A11YC_LANG_CHECKLIST_SRC_NONE', 'src属性値が空か存在しません');
define('A11YC_LANG_CHECKLIST_MUST_BE_NUMERIC', '%sの値は数値のみにしてください');

// bulk
define('A11YC_LANG_BULK_TITLE', '一括処理');
define('A11YC_LANG_BULK_UPDATE', '一括処理タイプ');
define('A11YC_LANG_BULK_UPDATE1', '新規チェック時の初期値として保存する（既存のページの試験結果を一切変更しません）');
define('A11YC_LANG_BULK_UPDATE2', 'チェックしたものだけを既存の試験結果に反映する（チェックを外す更新は行いません）');
define('A11YC_LANG_BULK_UPDATE3', 'すべての項目を既存の試験結果に反映する（注意：サイト内で全ページの試験結果が同じになります）');
define('A11YC_LANG_BULK_DONE', 'チェック終了');
define('A11YC_LANG_BULK_DONE1', '既存のページのチェックフラグを変更しない');
define('A11YC_LANG_BULK_DONE2', '既存のページをすべてチェック終了にする');
define('A11YC_LANG_BULK_DONE3', '既存のページをすべてチェック中にする');

// documents
define('A11YC_LANG_DOCS_TITLE', '参考資料');
define('A11YC_LANG_DOCS_EACH_TITLE', '参考資料');
define('A11YC_LANG_DOCS_ALL', 'すべて');
define('A11YC_LANG_DOCS_EACH_SUBTITLE', '「%s」について');
define('A11YC_LANG_DOCS_EACH_SUBTITLE_HOWTO', '「%s」の解説');
define('A11YC_LANG_DOCS_TEST', '試験方法について');
define('A11YC_LANG_DOCS_UNDERSTANDING', '達成基準&nbsp;%s&nbsp;を理解する');
define('A11YC_LANG_DOCS_SEARCH_RESULT_NONE', '該当する資料が見つかりませんでした');

// errors
define('A11YC_LANG_ERROR_COULD_NOT_GET_HTML', 'HTMLの取得に失敗しました: ');
define('A11YC_LANG_ERROR_BASIC_AUTH', '基本認証で守られているためアクセスできません。「設定」で基本認証用の情報を入力してください。');
define('A11YC_LANG_ERROR_BASIC_AUTH_WRONG', '基本認証用の情報が間違っているようです。「設定」を確認してください。');
define('A11YC_LANG_ERROR_SSL', 'SSLのサイトを対象とするときには、「設定」で対象となるドメインを入力してください。');
define('A11YC_LANG_ERROR_GET_NEW_A11YC', '<a href="%s">新しいバージョンのA11yc</a>があります（現在のバージョン: %s 最新のバージョン: %s）。');
define('A11YC_LANG_ERROR_NO_URL_NO_CHECK_SAME', 'URLがない場合は、リンク先とリンク文字列の整合性は確認できません。それ以外のチェックを行います。');
define('A11YC_LANG_ERROR_COULD_NOT_ESTABLISH_CONNECTION', 'なんらかの理由（SSLの証明書など）で、ソースを取得できませんでした。');

// startup
define('A11YC_LANG_STARTUP_SETDIRS', 'データ保存用ディレクトリとキャッシュディレクトリを設置しました。');
define('A11YC_LANG_STARTUP_ERROR_DIR', 'データ保存用ディレクトリとキャッシュディレクトリの設置に失敗しました。'.A11YC_DATA_PATH.'と'.A11YC_CACHE_PATH.'を設置してください。');

// disclosure
define('A11YC_LANG_DISCLOSURE_NEWEST_VERSION', '最新版');
define('A11YC_LANG_DISCLOSURE_CHANGE_VERSION', '方針・報告書・試験の版を切り替える');
define('A11YC_LANG_DISCLOSURE_VERSION_NOT_FOUND', '指定された版が見つかりませんでした');
define('A11YC_LANG_DISCLOSURE_PROTECT_VERSION_TITLE', 'データの保護');
define('A11YC_LANG_DISCLOSURE_PROTECT_VERSION_EXP', '現在の方針、試験、報告を保護します。保護されたデータは編集対象外になります。保護されたデータは、アクセシビリティ方針を表示する際、選択して閲覧できるようになります。保護は日単位です。本日のデータがある場合は上書きします。');
define('A11YC_LANG_DISCLOSURE_DELETE_SAMEDATE', '本日作成の保護されたデータがあったため上書きしました。');
define('A11YC_LANG_DISCLOSURE_PROTECT_DATA_SAVED', 'データを保護しました。');
define('A11YC_LANG_DISCLOSURE_PROTECT_DATA_FAILD', 'データの保護に失敗しました。');
define('A11YC_LANG_DISCLOSURE_PROTECT_DATA_CONFIRM', '本当にデータを保護してよいですか？');
define('A11YC_LANG_DISCLOSURE_VERSION_EXISTS', '存在するバージョン');
define('A11YC_LANG_DISCLOSURE_VERSION_EXISTS_EXP', 'それぞれのバージョンはアクセシビリティ報告書を表示しているページで切り替えられます。');

// sample
define('A11YC_LANG_SAMPLE_POLICY', '例文：\n<p>【あなたの名前／団体名】は、障害の有無や年齢などに関係なく、だれもが同じように利用できるアクセシブルなウェブサイトづくりに努めます。</p>\n<p>【このウェブサイトがアクセシビリティを確保することの意味を記載してください。】</p>\n<p>以下のとおりアクセシビリティ方針を定め、恒常的にアクセシビリティを確保してゆきます。</p>\n\n<h2>対応方針</h2>\n<p>当サイトは、日本工業規格JIS X 8341-3:2016「高齢者・障害者等配慮設計指針-情報通信における機器、ソフトウェア及びサービス-第3部:ウェブコンテンツ」の等級「AA」に準拠することを目標とします。</p>\n<p>本方針における「準拠」という対応度の表記は、情報通信アクセス協議会ウェブアクセシビリティ基盤委員会「<a href="http://waic.jp/docs/jis2016/compliance-guidelines/201603/">ウェブコンテンツのJIS X 8341-3:2016 対応度表記ガイドライン - 2016年3月版</a>」で定められた表記によります。</p>\n\n<h2>対象範囲</h2>\n<p>【「http://example.com 以下のページを対象とする」というように記載してください。】</p>\n\n<h2>達成目標日</h2>\n<p>【達成目標日を記載してください。】</p>\n\n<h2>例外事項</h2>\n<p>【もしあれば記載してください。】</p>');

// ua
define('A11YC_LANG_UA_USING',  '現在のブラウザ');
define('A11YC_LANG_UA_FEATUREPHONE',  'フィーチャーフォン');
define('A11YC_LANG_UA_IPHONE',  'スマートフォン（iPhone）');
define('A11YC_LANG_UA_IPAD',  'タブレット（iPad）');
define('A11YC_LANG_UA_ANDROID',  'スマートフォン（Android）');
define('A11YC_LANG_UA_ANDROID_TABLET',  'タブレット（Android）');
