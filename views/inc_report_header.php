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
.a11yc_header
{
	position: absolute;
	top: 10px;
	right: 20px;
	text-align: right;
	font-size: .7rem;
	line-height: 1.5;
}
/* table */
.a11yc_table {
	border-collapse: collapse;
	border: 1px solid #aaa;
	border-bottom: none;
	width: 100%;
}
.a11yc_table:not(.a11yc_issues):not(.a11yc_table_report):not(.a11yc_setting) tr:nth-child(even),
.a11yc_table:not(.a11yc_issues):not(.a11yc_table_report):not(.a11yc_setting) thead+tbody tr:nth-child(odd) {
}
.a11yc_table:not(.a11yc_issues):not(.a11yc_table_report):not(.a11yc_setting) thead+tbody tr:nth-child(even) {
	background-color: transparent;
}
.a11yc_table th,
.a11yc_table td {
	padding: 3px;
	border-bottom: 1px solid #aaa;
	vertical-align: top;
}
.a11yc_table th {
	max-width: 14em;
	min-width: 3em;
	text-align: left;
	white-space: nowrap;
	word-break: break-all;
}
.a11yc_table p {
	margin-top: 0;
	margin-bottom: .5em;
}
.a11yc_table ul {
	margin: 0;
	padding-left: 20px;
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
	.a11yc_header
	{
		display: none !important;
	}
	a:link,
	a:visited
	{
		color: #134A9C  !important;
	}
}
@page {
	size: A4;
	margin: 0;
}
</style>

</head>
<body>
<article>
<?php
echo '<div class="a11yc_header">'.Model\Setting::fetch('client_name').'</div>';

echo '<h1>'.$title.'</h1>';
?>
