<?php
/**
 * language
 *
 * @package    part of A11yc
 */

// general
define('A11YC_REF_WCAG20_URL', 'https://www.w3.org/TR/WCAG20/');
define('A11YC_REF_WCAG20_UNDERSTANDING_URL', 'https://www.w3.org/TR/UNDERSTANDING-WCAG20/');
define('A11YC_REF_WCAG20_TECH_URL', 'https://www.w3.org/TR/WCAG20-TECHS/');

define('A11YC_LANG_PRINCIPLE', 'Principle');
define('A11YC_LANG_GUIDELINE', 'Guideline');

define('A11YC_LANG_LEVEL', 'Level');
define('A11YC_LANG_EXIST', 'Present');
define('A11YC_LANG_EXIST_NON', 'Not Present');
define('A11YC_LANG_PASS', 'Conformance');
define('A11YC_LANG_PASS_NON', 'Non Conformance');
define('A11YC_LANG_PASS_PARTIAL', 'Partial Conformance');
define('A11YC_LANG_NOT_CHECKED', 'Not Yet');
define('A11YC_LANG_TEST_METHOD', 'Test Method');
define('A11YC_LANG_TEST_METHOD_AC', 'Automated Check');
define('A11YC_LANG_TEST_METHOD_AF', 'Automated Find');
define('A11YC_LANG_TEST_METHOD_HC', 'Human Check');
define('A11YC_LANG_CRITERION', 'Criterion');
define('A11YC_LANG_HERE', 'here, click here, click');
define('A11YC_LANG_TEST_RESULT', 'Test result');
define('A11YC_LANG_CURRENT_LEVEL', 'Current level of the page');
define('A11YC_LANG_ALT_URL_LEVEL', 'Level of <a href="%s">Alternative Content</a>');
define('A11YC_LANG_ALT_URL_EXCEPTION', 'Result including alternative content');
define('A11YC_LANG_CURRENT_LEVEL_WEBPAGES', 'Achieved level of the site');
define('A11YC_LANG_NUM_OF_CHECKED', 'Number of checked pages');
define('A11YC_LANG_CHECKED_PAGES', 'Target pages');
define('A11YC_LANG_UNPASSED_PAGES', 'Page less than the achievement grade as a target');
define('A11YC_LANG_UNPASSED_PAGES_NO', 'All checked page meet success criteria to target');
define('A11YC_LANG_RELATED', 'Related');
define('A11YC_LANG_VALUE', 'Value');
define('A11YC_LANG_AS', 'Accessibility Supported');
define('A11YC_LANG_UNDERSTANDING', 'Understanding WCAG2.0');
define('A11YC_LANG_NO_DOC', 'There is no document');
define('A11YC_LANG_JUMP_TO_CONTENT', 'Jump to content');
define('A11YC_LANG_BEGINNING_OF_THE_CONTENT', 'Start of main content');
define('A11YC_LANG_UPDATE_SUCCEED', 'Update Succeed');
define('A11YC_LANG_UPDATE_FAILED', 'Update Failed');
define('A11YC_LANG_CTRL_CONFIRM', 'It can not be returned. Are you sure you want to do %s?');
define('A11YC_LANG_CTRL_KEYWORD_TITLE', 'Keyword');
define('A11YC_LANG_CTRL_ORDER_TITLE', 'Order');
define('A11YC_LANG_CTRL_SEARCH', 'Search');
define('A11YC_LANG_CTRL_SEND', 'Send');
define('A11YC_LANG_CTRL_PREV', 'Previous');
define('A11YC_LANG_CTRL_NEXT', 'Next');
define('A11YC_LANG_CTRL_EXPAND', 'Expand');
define('A11YC_LANG_CTRL_COMPRESS', 'Compress');
define('A11YC_LANG_CTRL_VIEW', 'Show');
define('A11YC_LANG_CTRL_NAME', 'Name');
define('A11YC_LANG_CTRL_DATE', 'Date');
define('A11YC_LANG_CTRL_ADDNEW', 'Add New');
define('A11YC_LANG_CTRL_SAVE', 'Save');
define('A11YC_LANG_CTRL_PERSONS', 'Person');
define('A11YC_LANG_COUNT_ITEMS', '%s items');
define('A11YC_LANG_EXPORT_ERRORS_CSV',  'Export CSV');

// ua
define('A11YC_LANG_UA_USING',  'Current Browser');
define('A11YC_LANG_UA_FEATUREPHONE',  'Feature Phone');
define('A11YC_LANG_UA_IPHONE',  'iPhone');
define('A11YC_LANG_UA_IPAD',  'iPad');
define('A11YC_LANG_UA_ANDROID',  'Android');
define('A11YC_LANG_UA_ANDROID_TABLET',  'Android Tablet');

// login
define('A11YC_LANG_AUTH_TITLE', 'Login');
define('A11YC_LANG_LOGIN_USERNAME', 'Username');
define('A11YC_LANG_LOGIN_PASWWORD', 'Password');
define('A11YC_LANG_LOGIN_BTN', 'Login');
define('A11YC_LANG_LOGIN_ERROR0', 'Login was failed');
define('A11YC_LANG_LOGOUT', 'Logout');

// center
define('A11YC_LANG_CENTER_TITLE', 'Center');
define('A11YC_LANG_CENTER_BOOKMARKLET_EXP', 'Please register the following link into your browser\'s bookmark. You can be inspected any page.');
define('A11YC_LANG_CENTER_ABOUT', 'About A11yc');
define('A11YC_LANG_CENTER_LOGO', 'Logo');
define('A11YC_LANG_CENTER_ABOUT_CONTENT', 'A11yc is a web accessibility checker compatible with WCAG 2.0 created by Jidaikobo Inc.');

// pages
define('A11YC_LANG_PAGES_TITLE', 'Target Pages');
define('A11YC_LANG_PAGES_INDEX', 'Target Pages');
define('A11YC_LANG_PAGES_PAGETITLE', 'Target Page\'s title');
define('A11YC_LANG_PAGES_URLS', 'Target Page\'s URL');
define('A11YC_LANG_PAGES_URLS_ADD', 'Add URL');
define('A11YC_LANG_PAGES_URLS_ADD_FORCE', 'Add forcibly whether or not it exists');
define('A11YC_LANG_PAGES_NOT_FOUND', 'No pages to check found');
define('A11YC_LANG_PAGES_ALREADY_EXISTS', 'Already exists');
define('A11YC_LANG_PAGES_ADDED_NORMALLY', 'Add');
define('A11YC_LANG_PAGES_ADD_FAILED', 'Failed');
define('A11YC_LANG_PAGES_DONE', 'Done');
define('A11YC_LANG_PAGES_CHECK', 'Check');
define('A11YC_LANG_PAGES_LIVE', 'LIVE');
define('A11YC_LANG_PAGES_EXPORT', 'CSV');
define('A11YC_LANG_PAGES_DELETE', 'Delete');
define('A11YC_LANG_PAGES_UNDELETE', 'Undelete');
define('A11YC_LANG_PAGES_PURGE', 'Purge');
define('A11YC_LANG_PAGES_CREATED_AT', 'Add Date');
define('A11YC_LANG_PAGES_ORDER_CREATED_AT_ASC', 'Add Date Asc.');
define('A11YC_LANG_PAGES_ORDER_CREATED_AT_DESC', 'Add Date Desc.');
define('A11YC_LANG_PAGES_ORDER_TEST_DATE_ASC', 'Test Date Asc');
define('A11YC_LANG_PAGES_ORDER_TEST_DATE_DESC', 'Test Date Desc');
define('A11YC_LANG_PAGES_ORDER_URL_ASC', 'URL Asc.');
define('A11YC_LANG_PAGES_ORDER_URL_DESC', 'URL Desc.');
define('A11YC_LANG_PAGES_ORDER_TITLE_ASC', 'Page name Asc.');
define('A11YC_LANG_PAGES_ORDER_TITLE_DESC', 'Page name Desc.');
define('A11YC_LANG_PAGES_CTRL', 'Action');
define('A11YC_LANG_PAGES_URL_FOR_EACH_LINE', 'Enter one URL to each line, please press the "'.A11YC_LANG_PAGES_URLS_ADD.'".  Please once the registration is in the order of twenty. The program may be stopped at the registration process and too many.');
define('A11YC_LANG_PAGES_ALL', 'All');
define('A11YC_LANG_PAGES_YET', 'Yet');
define('A11YC_LANG_PAGES_TRASH', 'Trash');
define('A11YC_LANG_PAGES_GET_URLS', 'Get Urls');
define('A11YC_LANG_PAGES_GET_URLS_EXP', 'To generate a list of URL from a element of the target page. Recursive scan of the site structure is not supported.');
define('A11YC_LANG_PAGES_GET_URLS_BTN', A11YC_LANG_PAGES_GET_URLS);
define('A11YC_LANG_PAGES_INDEX_INFORMATION', '%s/%d Displayed from %d to %d');
define('A11YC_LANG_PAGES_DELETE_DONE', 'delete %s');
define('A11YC_LANG_PAGES_PURGE_DONE', 'purge %s');
define('A11YC_LANG_PAGES_UNDELETE_DONE', 'undelete %s');
define('A11YC_LANG_PAGES_DELETE_FAILED', 'Failed to delete %s');
define('A11YC_LANG_PAGES_PURGE_FAILED', 'Failed to purge %s');
define('A11YC_LANG_PAGES_UNDELETE_FAILED', 'Failed to undelete %s');
define('A11YC_LANG_PAGES_RETURN_TO_PAGES', 'A list of linked pages is found. please click this link and go back to "'.A11YC_LANG_PAGES_INDEX.'" to register.');
define('A11YC_LANG_PAGES_PRESS_ADD_BUTTON', 'A list of linked pages is found. After checking the contents of the list, please press "'.A11YC_LANG_PAGES_URLS_ADD.'" to register.');
define('A11YC_LANG_PAGES_NOT_FOUND_ALL', 'We could not find a valid link destination. Anyway, please click here to return to '.A11YC_LANG_PAGES_INDEX);
define('A11YC_LANG_PAGES_NOT_FOUND_SSL', 'In .htaccess, redirecting access to http to https may not find the link. Please try adding <code>RewriteCond %{QUERY_STRING} !a11yc=ssl</code> line to the condition of SSL related redirect after <code>RewriteEngine On</code> of .htaccess .');
define('A11YC_LANG_PAGES_ADD_TO_DATABASE', 'Register the URLs in the database');
define('A11YC_LANG_PAGES_ADD_TO_CANDIDATE', 'Acquire candidate URLs from HTML');
define('A11YC_LANG_PAGES_IT_TAKES_TIME', 'This process takes time.');
define('A11YC_LANG_PAGES_LABEL_EDIT', 'Edit');
define('A11YC_LANG_PAGES_LABEL_HTML_EXP', 'This HTML is used for judgment, such as when automatic acquisition of HTML fails. It will be rewritten automatically when the automatic acquisition succeeds in the future.');

// setup
define('A11YC_LANG_SETUP_TITLE', 'settings');
define('A11YC_LANG_SETTINGS_TITLE_BASE', 'BASIC SETTINGS');
define('A11YC_LANG_SETTINGS_TITLE_UA', 'UA');
define('A11YC_LANG_SETTINGS_TITLE_UA_EXP', 'You can add a UA by entering it in the last blank line. If '.A11YC_LANG_PAGES_PURGE.' is checked and sent, the user agent will be deleted but '.A11YC_LANG_UA_USING.' can not be deleted or edited except for its name.');
define('A11YC_LANG_SETTINGS_TITLE_VERSIONS', 'Versions');
define('A11YC_LANG_SETTINGS_TITLE_VERSIONS_EXP', 'You can name the version. Check '.A11YC_LANG_PAGES_PURGE.' and send it completely to delete that version. Please note that you can not recover. If you do not want general display, please uncheck '.A11YC_LANG_CTRL_VIEW.'.');
define('A11YC_LANG_SETUP_TITLE_ETC', 'etc.');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR', 'Checklist Behaviour');
define('A11YC_LANG_SETUP_BASE_URL', 'URL of Document Root');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR', 'Disappear when check');
define('A11YC_LANG_SETUP_BASIC_AUTH_TITLE', 'Basic Auth');
define('A11YC_LANG_SETUP_BASIC_AUTH_EXP', 'If the site to be tested is protected by basic authentication, please enter the user name and password for basic authentication here.');
define('A11YC_LANG_SETUP_BASIC_AUTH_USER', 'Basic Auth user');
define('A11YC_LANG_SETUP_BASIC_AUTH_PASS', 'Basic Auth password');
define('A11YC_LANG_SETUP_IS_USE_GUZZLE', 'Stop Guzzle');
define('A11YC_LANG_SETUP_IS_USE_GUZZLE_EXP', 'If Guzzle conflicts for some reason, please stop Guzzle. Even if you stop Guzzle, you can perform accessibility checks on the updated posts, but lose features such as report creation. If possible, please remove the cause.');

define('A11YC_LANG_DECLARE_DATE', 'Declare Date');
define('A11YC_LANG_STANDARD', 'Standard');
define('A11YC_LANG_DEPENDENCIES', 'List of dependent Web content technology');
define('A11YC_LANG_TEST_PERIOD', 'Test Period');
define('A11YC_LANG_TEST_DATE', 'Test Date');
define('A11YC_LANG_TARGET_LEVEL', 'Target Level');
define('A11YC_LANG_POLICY', 'Accessibility Policy');
define('A11YC_LANG_POLICY_DESC', 'Why you work in web accessibility ensure, goal date, corresponding policies, exceptions, please write and achieve grade to which you want to add to goal.');
define('A11YC_LANG_REPORT', 'Accessibility Report');
define('A11YC_LANG_OPINION', 'Opinion');
define('A11YC_LANG_REPORT_DESC', 'If you have any findings, etc., please describe.');
define('A11YC_LANG_CONTACT', 'Contact Us About Accessibility');
define('A11YC_LANG_CONTACT_DESC', 'Such as for accessibility of deficiencies, contact details of when that could not get the information, or please write the contact information on the web accessibility.');

define('A11YC_LANG_CANDIDATES_TITLE', 'Selected Method');
define('A11YC_LANG_CANDIDATES0', 'Each Page');
define('A11YC_LANG_CANDIDATES1', 'Select all of the web page');
define('A11YC_LANG_CANDIDATES2', 'Randomly selected');
define('A11YC_LANG_CANDIDATES3', 'Select a web page to represent the set of web pages');
define('A11YC_LANG_CANDIDATES4', 'Selected in accordance with a web page that you selected in web pages and random to represent the set of web pages');

define('A11YC_LANG_CANDIDATES_REASON', 'Page selection reason');
define('A11YC_LANG_CANDIDATES_IMPORTANT', 'Representative pages');
define('A11YC_LANG_CANDIDATES_RANDOM', 'Randomly selected pages');
define('A11YC_LANG_CANDIDATES_ALL', 'All pages are subject');
define('A11YC_LANG_CANDIDATES_PAGEVIEW', 'Pages with many accesses');
define('A11YC_LANG_CANDIDATES_NEW', 'New pages');
define('A11YC_LANG_CANDIDATES_ETC', 'Pages selected based on other criteria');

// checklist
define('A11YC_LANG_CHECKLIST_TARGETPAGE', 'Target Page');
define('A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR', 'The page does not exist');
define('A11YC_LANG_CHECKLIST_PAGE_NOT_FOUND_ERR_NO_SCHEME', 'do not omit http(s)://');
define('A11YC_LANG_CHECKLIST_NOT_FOUND_ERR', 'Could not find an error in the automatic check');
define('A11YC_LANG_CHECKLIST_COULD_NOT_DRAW_HTML', 'Failed to acquire HTML for some reason');
define('A11YC_LANG_CHECKLIST_TITLE', 'Checklist');
define('A11YC_LANG_CHECKLIST_DONE', 'Done');
define('A11YC_LANG_CHECKLIST_RESTOFNUM', 'Rest Of Numbers');
define('A11YC_LANG_CHECKLIST_TOTAL', 'Total');
define('A11YC_LANG_CHECKLIST_ACHIEVEMENT', 'Achieve Grade');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_FAILED', 'A nonconformity (noninterference conflict)');
define('A11YC_LANG_CHECKLIST_CONFORMANCE', 'Conformance with %s');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL', 'Partial Conformance with %s');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL', 'Additional Conformance');
define('A11YC_LANG_CHECKLIST_NON_INTERFERENCE', 'Non interference');
define('A11YC_LANG_CHECKLIST_ALT_URL', 'URL of alternative content');
define('A11YC_LANG_CHECKLIST_DO_LINK_CHECK', 'Do link check');
define('A11YC_LANG_CHECKLIST_DO_CSS_CHECK', 'Do external CSS check');
define('A11YC_LANG_CHECKLIST_SOURCE', 'Source code');
define('A11YC_LANG_CHECKLIST_VIEW_SOURCE', 'View source code');
define('A11YC_LANG_CHECKLIST_MACHINE_CHECK', 'Automatic Check');
define('A11YC_LANG_CHECKLIST_CHECK_RESULT', 'Check Result');
define('A11YC_LANG_CHECKLIST_UA', 'User Agent');
define('A11YC_LANG_CHECKLIST_REAL_URL', 'Checked URL');
define('A11YC_LANG_CHECKLIST_MEMO', 'Note');
define('A11YC_LANG_NO_BROKEN_LINK_FOUND', 'No broken link was found');
define('A11YC_LANG_CHECKLIST_PERCENTAGE', 'Achievement');
define('A11YC_LANG_CHECKLIST_NG_REASON', 'Nonconformity Reason');
define('A11YC_LANG_IMAGE', 'Image');
define('A11YC_LANG_IMPORTANCE', 'Importance');
define('A11YC_LANG_ELEMENT', 'Element');
define('A11YC_LANG_ATTRS', 'Attributes');
define('A11YC_LANG_IMPORTANT', 'IMPORTANT');
define('A11YC_LANG_NEED_CHECK', 'Need Check');
define('A11YC_LANG_CHECKLIST_IMPORTANT_EMP', 'Elements included in a element are displayed as "IMPORTANT".');
define('A11YC_LANG_CHECKLIST_IMPORTANT_EMP2', 'If "alt" is empty for the "important" element, "'.A11YC_LANG_NEED_CHECK.'" is displayed.');
define('A11YC_LANG_CHECKLIST_IMPORTANT_EMP3', 'You may see, <a href="%s">1.1.1</a>.');
define('A11YC_LANG_CHECKLIST_ALT_NULL', 'alt attribute not exist');
define('A11YC_LANG_CHECKLIST_ALT_EMPTY', 'alt attribute is empty');
define('A11YC_LANG_CHECKLIST_ALT_BLANK', 'alt attribute is blank character');
define('A11YC_LANG_CHECKLIST_SRC_NONE', 'src attribute is empty or does not exist');
define('A11YC_LANG_CHECKLIST_MUST_BE_NUMERIC', 'value of %s must be numeric');
define('A11YC_LANG_CHECKLIST_BACK_TO_MESSAGE', 'back to message');
define('A11YC_LANG_CHECKLIST_SEE_DETAIL', 'See How to resolve');
define('A11YC_LANG_CHECKLIST_SEE_UNDERSTANDING', 'See understanding WCAG');

// bulk
define('A11YC_LANG_BULK_TITLE', 'Batch');
define('A11YC_LANG_BULK_UPDATE', 'Batch Type');
define('A11YC_LANG_BULK_UPDATE1', 'Save as the initial value at the new check (does not change any of the test results of an existing page)');
define('A11YC_LANG_BULK_UPDATE2', 'Only those that checked reflected in the existing test results (but does not update to remove the check)');
define('A11YC_LANG_BULK_UPDATE3', 'Reflect all of the items to the existing test results (Note: The test results of all the pages will be the same in the site)');
define('A11YC_LANG_BULK_DONE', 'Check the end ');
define('A11YC_LANG_BULK_DONE1', 'Do not change the check flag of an existing page');
define('A11YC_LANG_BULK_DONE2', 'To check the end all existing page');
define('A11YC_LANG_BULK_DONE3', 'To un-check all of the existing page');

// documents
define('A11YC_LANG_DOCS_TITLE', 'Documents');
define('A11YC_LANG_EACTH_DOCS_TITLE', 'Documents');
define('A11YC_LANG_DOCS_ALL', 'All');
define('A11YC_LANG_DOCS_EACH_SUBTITLE', 'About "%s"');
define('A11YC_LANG_DOCS_EACH_SUBTITLE_HOWTO', 'Document of "%s"');
define('A11YC_LANG_DOCS_TEST', 'About Testing');
define('A11YC_LANG_DOCS_UNDERSTANDING', 'Understanding SC&nbsp;%s');
define('A11YC_LANG_DOCS_SEARCH_RESULT_NONE', 'Appropriate documentation was not found.');

// errors
define('A11YC_LANG_ERROR_COULD_NOT_GET_HTML', 'Acquisition of HTML failed: ');
define('A11YC_LANG_ERROR_BASIC_AUTH', 'Access is not possible because it is protected by basic authentication. Please enter the information for Basic Authentication in "Setting".');
define('A11YC_LANG_ERROR_BASIC_AUTH_WRONG', 'Information for basic authentication seems to be wrong. Please check "Setting".');
define('A11YC_LANG_ERROR_SSL', 'When targeting SSL sites, please enter the target domain in "Settings".');
define('A11YC_LANG_ERROR_GET_NEW_A11YC', '<a href="%s">There is a new version of A11yc</a> (current version:%s latest version:%s).');
define('A11YC_LANG_ERROR_NO_URL_NO_CHECK_SAME', 'Without URL, link destination and link string can not be confirmed. Executed other checks.');
define('A11YC_LANG_ERROR_COULD_NOT_ESTABLISH_CONNECTION', 'For some reason (ex. SSL certificate), Could not get the source.');

// results
define('A11YC_LANG_RESULTS_TITLE', A11YC_LANG_TEST_RESULT);
define('A11YC_LANG_RESULTS_NEWEST_VERSION', 'Newest version');
define('A11YC_LANG_RESULTS_CHANGE_VERSION', 'Switch policy, report, exam version');
define('A11YC_LANG_RESULTS_VERSION_NOT_FOUND', 'Specified version was not found');
define('A11YC_LANG_RESULTS_PROTECT_VERSION_TITLE', 'Data protection');
define('A11YC_LANG_RESULTS_PROTECT_VERSION_EXP', 'Protect current policies, tests and reports. Protected data is not subject to editing. Protected data will be laundered and readable when displaying accessibility policy. Protection is on a daily basis. Overwrite existing data if there is today\'s data.');
define('A11YC_LANG_RESULTS_DELETE_SAMEDATE', 'Since there was protected data created today, overwrote it.');
define('A11YC_LANG_RESULTS_PROTECT_DATA_SAVED', 'Data was protected.');
define('A11YC_LANG_RESULTS_PROTECT_DATA_FAILD', 'Data protection failed.');
define('A11YC_LANG_RESULTS_PROTECT_DATA_CONFIRM', 'Do you really want to protect your data?');
define('A11YC_LANG_RESULTS_VERSION_EXISTS', 'Versions');
define('A11YC_LANG_RESULTS_PASSED_CHECKITEM', 'Passed Check Item');

// issues
define('A11YC_LANG_ISSUES_TITLE', 'Issue');
define('A11YC_LANG_ISSUES_ADD', 'Add Issue');
define('A11YC_LANG_ISSUES_EDIT', 'Edit Issue');
define('A11YC_LANG_ISSUES_IS_COMMON_EXP', 'Please check if there is a common problem in the site. All checklists will be treated as a problem');
define('A11YC_LANG_ISSUES_IS_COMMON', 'Common Problem');
define('A11YC_LANG_ISSUES_HTML', 'HTML piece of the problem part');
define('A11YC_LANG_ISSUES_HTML_EXP', 'When you enter the HTML piece of the problem part, it is displayed in the report etc. HTML can not include a comment-out part, the inside of script, etc.');
define('A11YC_LANG_ISSUES_N_OR_E', 'Notice/Error');
define('A11YC_LANG_ISSUES_N_OR_E_EXP', 'Choose Notice or Error');
define('A11YC_LANG_ISSUES_TECH', 'URL of Techniques for WCAG 2.0');
define('A11YC_LANG_ISSUES_TECH_EXP', 'Please enter URL of Techniques for WCAG 2.0. If there are multiple, please separate them with line breaks');
define('A11YC_LANG_ISSUES_STATUS', 'Status');
define('A11YC_LANG_ISSUES_STATUS_1', 'Not Yet');
define('A11YC_LANG_ISSUES_STATUS_2', 'Doing');
define('A11YC_LANG_ISSUES_STATUS_3', 'Done');
define('A11YC_LANG_ISSUES_ERRMSG', 'Error Message');
define('A11YC_LANG_ISSUES_ERRMSG_EXP', 'Error messages displayed in Live mode, CSV, etc.');
define('A11YC_LANG_ISSUES_ADDED', 'Issue was added');
define('A11YC_LANG_ISSUES_ADDED_FAILED', 'Issue was not added');
define('A11YC_LANG_ISSUES_EDITED', 'Issue was edited');
define('A11YC_LANG_ISSUES_EDITED_FAILED', 'Issue was not edited');
define('A11YC_LANG_ISSUES_MESSAGE', 'Messages');
define('A11YC_LANG_ISSUES_MESSAGE_ADD', 'Add Message');

// image list
define('A11YC_LANG_IMAGES_TITLE', 'Image List');

// errors
define('A11YC_LANG_ERROR_NON_TARGET_LEVEL', 'Please perform the check from select "Target Level" in setting.');
define('A11YC_LANG_ERROR_NON_BASE_URL', 'Please Input "'.A11YC_LANG_SETTINGS_BASE_URL.'" in setting.');

// sample
define('A11YC_LANG_SAMPLE_POLICY', 'sample:\n<p>[Your Name / Organization Name] will endeavor to create an accessible website that anyone can use in the same way regardless of the presence or absence of disability or age.</p>\n<p>[Please describe the meaning of this website to secure accessibility.]</p>\n<p>We set up accessibility policy as follows and will constantly secure accessibility.</p>\n\n<h2>Policy</h2>\n<p>[Please describe the accessibility policy.]</p>\n\n<h2>Scope of coverage</h2>\n<p>[Please describe as "target pages below http://example.com".]</p>\n\n<h2>Achievement target date</h2>\n<p>[Please describe the target date of accomplishment.]</p>\n\n<h2>Exceptions</h2>\n<p>[Please list if any.]</p>');

// stand alones
define('A11YC_LANG_POST_SERVICE_NAME', 'A11yc Accessibility Validation Service');
define('A11YC_LANG_POST_SERVICE_NAME_ABBR', 'A11yc AVS');

define('A11YC_LANG_POST_INDEX', 'Validation');

define('A11YC_LANG_POST_DESCRIPTION', 'This is a kind of web accessibility checker. anyone can use.');

define('A11YC_LANG_POST_README', 'Read me');

define('A11YC_LANG_POST_HOWTO', '<p>Mechanical accessibility check for the HTML. Please paste your HTML in the textarea of ​​<code>HTML Source</code> or enter target URL to <code>URL</code> in <a href="%s">Validation</a> and send it. Display accessibility checkpoints and their commentary.</p><p>In the case of check by URL, in addition to checking accessibility, you can check images and alt.</p>');

define('A11YC_LANG_POST_SERVICE_NAME_TITLE', 'Name of service');
define('A11YC_LANG_POST_SERVICE_NAME_EXP', '"<strong>'.A11YC_LANG_POST_SERVICE_NAME.'</strong>" Short version is "<strong>'.A11YC_LANG_POST_SERVICE_NAME_ABBR.'</strong>".');

define('A11YC_LANG_POST_CONDITION_TITLE', 'Limit');
define('A11YC_LANG_POST_CONDITION_EXP_FREE', 'It is free.');
define('A11YC_LANG_POST_CONDITION_EXP_10MIN', 'More than 10 posts in 10 minutes, you can not post for a few minutes.');
define('A11YC_LANG_POST_CONDITION_EXP_24H', 'More than 150 posts from one IP address in 24 hours, you will not be able to post for a while.');

define('A11YC_LANG_POST_COLLECTION_TITLE', 'Collected information');
define('A11YC_LANG_POST_COLLECTION_GOOGLE', 'We are using Google Analytics.');
define('A11YC_LANG_POST_COLLECTION_IP', 'Because of limitation by IP address, IP address and time accessed from that IP address are saved.');

define('A11YC_LANG_POST_VENDOR_TITLE', 'Vendor');
define('A11YC_LANG_POST_VENDOR_JIDAIKOBO', 'Jidaikobo Inc.');
define('A11YC_LANG_POST_VENDOR_JIDAIKOBO_TWITTER', 'Twitter of Jidaikobo Inc.');

define('A11YC_LANG_POST_TECH_TITLE', 'Technical information');
define('A11YC_LANG_POST_TECH_A11YC', 'Based on the library called A11yc. <a href="https://github.com/jidaikobo-shibata/a11yc">A11yc is available on github </a>. Is is also made by Jidaikobo.');
define('A11YC_LANG_POST_TECH_A11YC_ADD', 'In A11yc, there are functions to prepare reports and test results (check list)');
define('A11YC_LANG_POST_TECH_JWP_A11YC', 'Plugin for WordPress <a href="https://en.wordpress.org/plugins/jwp-a11y/">jwp-a11y</a> also has the same function as A11yc, so please do it.');
define('A11YC_LANG_POST_TECH_JWP_A11YC_ADD', 'In Wordpress Plugin, execute validation function of A11yc AVS every time posting, constantly manage the site with accessibility awareness.');

define('A11YC_LANG_POST_FEEDBACK_TITLE', 'Feedback');
define('A11YC_LANG_POST_FEEDBACK_EXP', 'If there is a request or correction part on the expression of the function, commentary, etc., please send us email (<a href="mailto:info@jidaikobo.com">info@jidaikobo.com</a>) or Twitter.');

define('A11YC_LANG_POST_DONE', 'Done.');
define('A11YC_LANG_POST_DONE_POINTS', '%s Warnned Points.');
define('A11YC_LANG_POST_DONE_NOTICE_POINTS', '%s Noitced Points.');
define('A11YC_LANG_POST_DONE_IMAGE_LIST', 'Displaying list of images and alt. Depending on the referrer settings, images may not be displayed.');
define('A11YC_LANG_POST_NO_IMAGES_FOUND', 'No Images found');

define('A11YC_LANG_POST_DO_CHECK', 'Do Accessibility check');
define('A11YC_LANG_POST_SHOW_LIST_IMAGES', 'Show list of images and alt');
define('A11YC_LANG_POST_BEHAVIOUR', 'Behaviour');
define('A11YC_LANG_POST_CANT_SHOW_LIST_IMAGES', 'can not display images and alt lists with HTML source check.');

define('A11YC_LANG_POST_BASIC_AUTH_EXP', 'Could not pass basic authentication');

define('A11YC_LANG_POST_SOCIAL_TWEET', 'Tweet');
define('A11YC_LANG_POST_SOCIAL_FACEBOOK', 'Facebook Like Button');
define('A11YC_LANG_POST_SOCIAL_HATENA', 'Add Hatena Bookmark');
