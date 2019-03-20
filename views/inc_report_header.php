<?php namespace A11yc; ?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> - A11yC</title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

<style type="text/css">
body
{
	font-family: sans-serif;
	font-size: .9rem;
	width: 100%;
	max-width: 860px;
	margin: auto;
	position: relative;
}
body *
{
	box-sizing: border-box;
	word-break: break-all;
}
body > *
{
	max-width: 100%;
}

article
{
	position: relative;
}
article:first-of-type .a11yc_header
{
	position: absolute;
	top: 10px;
	right: 20px;
	text-align: right;
	font-size: .7rem;
	line-height: 1.5;
}
article:not(:first-of-type)
{
	margin-top: 5em;
}
article:not(:first-of-type) .a11yc_header
{
	display: none;
}
h2,
h3
{
	font-size: 1rem;
	margin-top: 1.5em;
	margin-bottom: .25em;
}
h3
{
	font-size: .825rem;
}
p
{
	font-size: .825rem;
	margin: 0;
}
/* table */
.a11yc_table {
	border-collapse: collapse;
	border: 1px solid #aaa;
	width: 100%;
	font-size: .825em;
}
h1 + .a11yc_table
{
	width: auto;
}
h1 + .a11yc_table th,
h1 + .a11yc_table td
{
	padding :1px  2mm 1px 1px;
}


.a11yc_table th,
.a11yc_table td {
	vertical-align: top;
	border-top: 1px solid #aaa;
	border-bottom: 1px solid #aaa;
}
.a11yc_table thead th {
	min-width: 3em;
	text-align: left;
	white-space: nowrap;
	word-break: break-all;
}
.a11yc_table tbody th
{
	text-align: left;

}
.a11yc_table th:first-child:last-child
{
	background-color: #eee;
	padding: 5px 1px;
}
.a11yc_table thead tr:last-child th:not(:first-child)
{
	text-align: center;
}
.a11yc_table .a11yc_result_string
{
	width: 70vw;
}
:not(h1) + .a11yc_table  th,
:not(h1) + .a11yc_table  td
{
	padding: 1px 3px;
}
.a11yc_table p {
	margin-top: 0;
	margin-bottom: .5em;
}
.a11yc_table ul {
	margin: 0;
	padding-left: 20px;
}
.a11yc_table li {
	line-height: 1.2;
}
.a11yc_table td.a11yc_memo {
	max-width: 50%;
	width: 45%;
}
.a11yc_table td.a11yc_level {
	text-align: center;
}
.a11yc_table th.a11yc_result_exist,
.a11yc_table td.a11yc_result_exist {
	text-align: center;
	white-space: nowrap;
}
.a11yc_table td.a11yc_pass_str {
	text-align: center;
	white-space: nowrap;
}

	.noprint
	{
		display: none !important;
	}
	html
	{
		font-size: .8em;
	}
	body
	{
		max-width: 190mm;
	}
	.a11yc_header
	{
		display: none !important;
	}
	article
	{
		page-break-before: always;
		display: table;
		width: 100%;
		height: 290mm;
		padding: 0 ;
		min-height: auto;
	}
	.no_page_break
	{
		page-break-inside: avoid;
	}
	article:not(:first-of-type)
	{
		margin-top: 0;
	}
	a:link,
	a:visited
	{
		color: #134A9C  !important;
	}
@page {
	size: A4;
	margin: 15mm 10mm;
	}
</style>

</head>
<body>
<?php
// echo '<div class="a11yc_header">'.Model\Setting::fetch('client_name').'</div>';

echo '<h1>'.$title.'</h1>';
?>
