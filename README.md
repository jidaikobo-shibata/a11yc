# a11yc
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Code Climate](https://codeclimate.com/github/jidaikobo-shibata/a11yc/badges/gpa.svg)](https://codeclimate.com/github/jidaikobo-shibata/a11yc)
[![Build Status](https://travis-ci.org/jidaikobo-shibata/a11yc.svg?branch=master)](https://travis-ci.org/jidaikobo-shibata/a11yc)

## screenshot

<img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/machine-check.png" width="300" alt="machine check"> <img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/checklist.png" width="300" alt="checklist"> <img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/pages.png" width="300" alt="list of pages"> <img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/results.png" width="300" alt="evaluate results">

##[en]

### Introduction

Check accessibility of target page and generate accessibility evaluate page and policy.

See how it works.  [A11yc Accessibility Validation Service](https://a11yc.com/validator/en/index.php)
[WordPress Plugin jwp-a11y](https://wordpress.org/plugins/jwp-a11y/)

### deploy

Upload files and duplicate

 config/config.dist.php

to

 config/config.php

set A11YC_URL, A11YC_USERS, A11YC_LANG.

##[ja]

### 紹介

JIS X 8341-3:2016 (WCAG 2.0) に基づいたアクセシビリティ報告書と方針を生成するためのウェブプリケーションです。

[A11yc Accessibility Validation Service](https://a11yc.com/validator/index.php)で動いているものを確認できます。
[WordPressプラグイン版 jwp-a11y](https://ja.wordpress.org/plugins/jwp-a11y/)もあります。

### 設置方法

サーバにファイル一式をアップしたのち、

 config/config.dist.php

を複製して、

 config/config.php

を作り、環境設定してください。ほとんどの場合、A11YC_URL (ファイルを設置したアドレス) とA11YC_USERS (管理者情報) を設定したら大丈夫だと思います。

報告書作成画面にアクセスできるIPを制限したい場合はA11YC_APPROVED_IPSを書いてください。特にない場合は、define()しないようにしてください。

パスワードはconfig.dist.phpにもありますが、コマンドラインで

  php -r "echo password_hash('password', CRYPT_BLOWFISH);\n"

というようにハッシュして保存してください。

### 使い方

最初に「設定」で、「目標とする適合レベル」を定めてください。

そのあと、「チェック」で対象のページを追加していって、チェックをしてゆきます。

### 報告書と方針

 report.dist.php

を適当なファイル名に変更します。PHPがわかる方なら、適当にパスをいじって好きなところにおいてください。わからない場合でも、HTMLはいじっても大丈夫ですので、

 report.php

あたりにrenameしてください。

ブラウザでこのファイルにアクセスすれば、状況に応じた報告書が生成されています。

### CMSへの取り込み

WordPressのプラグインでは、投稿のたびに投稿内容のアクセシビリティチェックを行うようになっています。その手順を下記します。

A11ycのクローラにチェックするページのURLを渡します。WordPressの場合は、get_permalink($post->ID)のようなものです。

  \A11yc\Crawl::set_target_path('URL');

\A11yc\Validate::$codesには、チェックする項目が入っています。これを一旦$codesに代入します。

  $codes = \A11yc\Validate::$codes;

HTMLのすべてをチェックするときには、headの中などのチェックも行いますが、投稿内容のみをチェックする場合は、不要なので、set_is_partial()をtrueにします。

  \A11yc\Validate::set_is_partial(true);

Validateの中のリンクチェッカは処理に時間がかかるので、何らかの方法で、オンオフできるようにします。

  \A11yc\Validate::set_do_link_check(\A11yc\Input::post('jwp_a11y_link_check'));

Validateクラスに検査対象のHTMLをセットします。以下はWordPressの例です。

  \A11yc\Validate::set_html($post->post_content);

$codesをループで回すことで、セットされたHTMLを検査していきます。

  foreach ($codes as $method => $class)
  {
    $class::$method();
  }

あとは \A11yc\Validate::get_error_ids() にエラーが積まれているので、これをどうにか表示します。

### 参考にしたものの一部

* [JIS X 8341-3:2016 対応発注ガイドライン](http://waic.jp/docs/jis2016/order-guidelines/201604/)
* [アクセシビリティ・サポーテッド（AS）情報](http://waic.jp/docs/as/)
* [JIS X 8341-3:2016 試験実施ガイドライン](http://waic.jp/docs/jis2016/test-guidelines/201604/)
* [ウェブアクセシビリティ評価ツールの最低要求仕様 2012年11月版](http://waic.jp/docs/jis2010/minimum-requirement/201211/index.html)
* [WCAG-EM Report Tool Website Accessibility Evaluation Report Generator](https://www.w3.org/WAI/eval/report-tool/#/)
* [HTML5 4.7.1.1.22 Guidance for conformance checkers](https://www.w3.org/TR/html5/embedded-content-0.html#guidance-for-conformance-checkers)
* [HTML5.1 4.7.5.1.23. Guidance for conformance checkers](https://www.w3.org/TR/html51/semantics-embedded-content.html#guidance-for-conformance-checkers)

### 依存しているライブラリ

* [spyc](https://github.com/mustangostang/spyc)
* [guzzle](https://github.com/guzzle/guzzle)

### チェック

[skipfish](https://code.google.com/archive/p/skipfish/)と[OWASP ZAP](https://www.owasp.org/index.php/OWASP_Zed_Attack_Proxy_Project)をもちいて脆弱性チェックをかけています。

### 謝辞

参考にさせていただいた資料および使わせてもらっているライブラリの開発者の皆さんに感謝します。
