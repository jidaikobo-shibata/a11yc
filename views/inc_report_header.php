<?php namespace A11yc; ?><!DOCTYPE html>
<html lang="<?php echo A11YC_LANG ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?> - A11yC</title>

	<!-- robots -->
	<meta name="robots" content="noindex, nofollow">

	<!-- viewport -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0">

	<!--css-->
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
body > article
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
h2
{
	font-size: 1rem;
	margin-top: 1.5em;
	margin-bottom: .25em;
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
h1 + .a11yc_table th
{
	padding-right: 5mm;
}
.a11yc_table th,
.a11yc_table td {
	vertical-align: top;
	border-top: 1px solid #aaa;
	border-bottom: 1px solid #aaa;
}
.a11yc_table th {
	min-width: 3em;
	text-align: left;
	white-space: nowrap;
	word-break: break-all;
}
.a11yc_table .a11yc_result_string
{
	width: 45%;
}
h2 + .a11yc_table tr > :last-child
{
	width: 1px;
}
h2 + .a11yc_table  th,
h2 + .a11yc_table  td
{
	padding: 1px 3px;
}
.a11yc_table thead tr > :nth-child(2),
.a11yc_table tr > :nth-child(3),
.a11yc_table tr > :nth-child(4),
.a11yc_table tr > :nth-child(5)
{
	text-align: center;
	white-space: nowrap;
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
@media print {
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
	body > article
	{
		page-break-before: always;
		display: table;
		width: 100%;
		height: 290mm;
		padding: 0 ;
		min-height: auto;
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
}
@page {
	size: A4;
	margin: 15mm 0 10mm;
	}
</style>

</head>
<body>
<article>
<?php
echo '<div class="a11yc_header">'.Model\Setting::fetch('client_name').'</div>';

echo '<h1>'.$title.'</h1>';
?>
