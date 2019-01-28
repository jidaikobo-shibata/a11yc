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
/* table */
.a11yc_table {
	border-collapse: collapse;
	border: 1px solid #aaa;
	border-bottom: none;
	width: 100%;
}
.a11yc_table:not(.a11yc_issues):not(.a11yc_table_report):not(.a11yc_setting) tr:nth-child(even),
.a11yc_table:not(.a11yc_issues):not(.a11yc_table_report):not(.a11yc_setting) thead+tbody tr:nth-child(odd) {
	background-color: #f3f3f3;
}
.a11yc_table:not(.a11yc_issues):not(.a11yc_table_report):not(.a11yc_setting) thead+tbody tr:nth-child(even) {
	background-color: transparent;
}
.a11yc_table th,
.a11yc_table td {
	padding: 5px;
	border-bottom: 1px solid #aaa;
	vertical-align: top;
}
.a11yc_table th {
	max-width: 14em;
	min-width: 3em;
	text-align: left;
	white-space: normal;
	word-break: break-all;
}
.a11yc_table p {
	margin-top: 0;
	margin-bottom: .5em;
}
</style>

</head>
<body>
<?php
echo '<div class="a11yc_header">'.Model\Setting::fetch('client_name').'</div>';

echo '<h1>'.$title.'</h1>';
?>
