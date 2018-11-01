# a11yc
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Code Climate](https://codeclimate.com/github/jidaikobo-shibata/a11yc/badges/gpa.svg)](https://codeclimate.com/github/jidaikobo-shibata/a11yc)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jidaikobo-shibata/a11yc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jidaikobo-shibata/a11yc/?branch=master)
[![Build Status](https://travis-ci.org/jidaikobo-shibata/a11yc.svg?branch=master)](https://travis-ci.org/jidaikobo-shibata/a11yc)

## screenshot

<img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/checklist_en.png" width="300" alt="checklist - English"> <img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/checklist_ja.png" width="300" alt="checklist - Japanese">

## [en]

### Introduction

Check accessibility of target page and generate accessibility evaluate page and policy.

See how it works.  [A11yc Accessibility Check Service](https://a11yc.com/check/en/index.php)
[WordPress Plugin jwp-a11y](https://wordpress.org/plugins/jwp-a11y/)

### deploy

Upload files and duplicate

```
a11yc/config/config.dist.php
```

to

```
a11yc/config/config.php
```

set A11YC_URL, A11YC_USERS, A11YC_LANG.

if you want to use SQLITE, create directory

```
a11yc/db
```

and create symlinks

```
ln -s a11yc/assets
ln -s a11yc/index.php
ln -s a11yc/post.php
```

mv a11yc/.htaccess.dist to document root and rename it.

```
mv a11yc/.htaccess.dist .htaccess
```

## [ja]

### 紹介

JIS X 8341-3:2016 (WCAG 2.0) に基づいたアクセシビリティ報告書と方針を生成するためのウェブプリケーションです。

[A11yc Accessibility Check Service](https://a11yc.com/check/index.php)で動いているものを確認できます。
[WordPressプラグイン版 jwp-a11y](https://ja.wordpress.org/plugins/jwp-a11y/)もあります。

### 設置方法

サーバにファイル一式をアップしたのち、

```
config/config.dist.php
```

を複製して、

```
config/config.php
```

を作り、環境設定してください。ほとんどの場合、A11YC_URL (ファイルを設置したアドレス) とA11YC_USERS (管理者情報) を設定したら大丈夫だと思います。

SQLITEを使う場合は、

```
a11yc/db
```

を設置してください。

フロントコントローラへのシンボリックリンクをはります。

```
ln -s a11yc/assets
ln -s a11yc/index.php
ln -s a11yc/post.php
```

ドキュメントルートに.htaccess.distを.htaccessとして設置してください。

```
mv a11yc/.htaccess.dist .htaccess
```

報告書作成画面にアクセスできるIPを制限したい場合はA11YC_APPROVED_IPSを書いてください。特にない場合は、define()しないようにしてください。

パスワードはconfig.dist.phpにもありますが、コマンドラインで

```
php -r "echo password_hash('password', CRYPT_BLOWFISH);\n"
```

というようにハッシュして保存してください。

### 使い方

最初に「設定」で、「目標とする適合レベル」を定めてください。

そのあと、「チェック」で対象のページを追加していって、チェックをしてゆきます。

### 報告書と方針

```
a11yc/report.dist.php
```

を適当なファイル名に変更します。PHPがわかる方なら、適当にパスをいじって好きなところにおいてください。わからない場合でも、HTMLはいじっても大丈夫ですので、

```
report.php
```

あたりにrenameしてください。

ブラウザでこのファイルにアクセスすれば、状況に応じた報告書が生成されています。

### CMSへの取り込み

WordPressのプラグインでは、投稿のたびに投稿内容のアクセシビリティチェックを行うようになっています。その手順を下記します。

A11ycでチェックするために記事のURLを取得します。WordPressの場合は、get_permalink($post->ID)のようなものです。一時的なチェックの場合は、サイトトップのURLを入れても動きます。

```
require_once ('/path/to/a11yc/main.php');
$url = get_permalink($post->ID);
```

HTMLのすべてをチェックするときには、headの中などのチェックも行いますが、投稿内容のみをチェックする場合は、不要なのでそのように設定します。

```
\A11yc\Validate::$is_partial = true;
```

Validateの中のリンクチェッカは処理に時間がかかるので、何らかの方法で、オンオフできるようにします。

```
\A11yc\Validate::$do_link_check = \A11yc\Input::post('jwp_a11y_link_check', false);
```

CSSのチェックも普段は不要かもしれません。

```
\A11yc\Validate::$do_css_check  = \A11yc\Input::post('jwp_a11y_css_check', false);
```

Validateクラスに検査対象のHTMLをセットします。以下はWordPressの例です。

```
\A11yc\Validate::html($url, apply_filters('the_content', $obj->post_content));
```

チェックの後、

- \A11yc\Validate\Get::errors($url)にエラー
- \A11yc\Validate\Get::errorCnts($url)にエラーの数
- \A11yc\Validate\Get::logs($url)にチェック内容

が積まれているので、これを取得できます。

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
