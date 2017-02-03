# a11yc
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Code Climate](https://codeclimate.com/github/jidaikobo-shibata/a11yc/badges/gpa.svg)](https://codeclimate.com/github/jidaikobo-shibata/a11yc)
[![Build Status](https://travis-ci.org/jidaikobo-shibata/a11yc.svg?branch=master)](https://travis-ci.org/jidaikobo-shibata/a11yc)

## screenshot

<img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/machine-check.png" width="300" alt="machine check"> <img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/checklist.png" width="300" alt="checklist"> <img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/pages.png" width="300" alt="list of pages"> <img src="https://raw.githubusercontent.com/jidaikobo-shibata/a11yc/master/screenshots/results.png" width="300" alt="evaluate results">

##[en]

### Introduction

Check accessibility of target page and generate accessibility evaluate page and policy.

currently documents are Japanese only.  eventually, we will translate to English, at least.

##[ja]

### 紹介

JIS X 8341-3:2016 (WCAG 2.0) に基づいたアクセシビリティ報告書と方針を生成するためのウェブプリケーションです。

### 設置方法

サーバにファイル一式をアップしたのち、

 config/config.dist.php

を複製して、

 config/config.php

を作り、環境設定してください。ほとんどの場合、A11YC_URL (ファイルを設置したアドレス) とA11YC_USERS (管理者情報) を設定したら大丈夫だと思います。

### 使い方

最初に「設定」で、「目標とする適合レベル」を定めてください。

そのあと、「チェック」で対象のページを追加していって、チェックをしてゆきます。

### 報告書と方針

 report.dist.php

を適当なファイル名に変更します。PHPがわかる方なら、適当にパスをいじって好きなところにおいてください。わからない場合でも、HTMLはいじっても大丈夫ですので、

 report.php

あたりにrenameしてください。

ブラウザでこのファイルにアクセスすれば、状況に応じた報告書が生成されています。

### 参考にしたものの一部

* [JIS X 8341-3:2016 対応発注ガイドライン](http://waic.jp/docs/jis2016/order-guidelines/201604/)
* [アクセシビリティ・サポーテッド（AS）情報](http://waic.jp/docs/as/)
* [JIS X 8341-3:2016 試験実施ガイドライン](http://waic.jp/docs/jis2016/test-guidelines/201604/)
* [ウェブアクセシビリティ評価ツールの最低要求仕様 2012年11月版](http://waic.jp/docs/jis2010/minimum-requirement/201211/index.html)
* [WCAG-EM Report Tool Website Accessibility Evaluation Report Generator](https://www.w3.org/WAI/eval/report-tool/#/)

### チェック

[skipfish](https://code.google.com/archive/p/skipfish/)をもちいて脆弱性チェックをかけています。
