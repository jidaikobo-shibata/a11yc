<?php
/**
 * language
 *
 * @package    part of A11yc
 */

// general
define('A11YC_LANG_PRINCIPLE', 'Principle');
define('A11YC_LANG_GUIDELINE', 'Guideline');

define('A11YC_LANG_LEVEL', 'Level');
define('A11YC_LANG_EXIST', 'Present');
define('A11YC_LANG_EXIST_NON', 'Not Present');
define('A11YC_LANG_PASS', 'Conformance ');
define('A11YC_LANG_CRITERION', 'Criterion');
define('A11YC_LANG_HERE', 'here');
define('A11YC_LANG_TEST_RESULT', 'Test result');
define('A11YC_LANG_CURRENT_LEVEL', 'Current Level');
define('A11YC_LANG_CURRENT_LEVEL_WEBPAGES', 'Level that has been achieved at the site');
define('A11YC_LANG_NUM_OF_CHECKED', 'Number of checked pages');
define('A11YC_LANG_CHECKED_PAGES', 'Target pages');
define('A11YC_LANG_UNPASSED_PAGES', 'Page less than the achievement grade as a target');
define('A11YC_LANG_UNPASSED_PAGES_NO', 'All checked page meet success criteria to target');
define('A11YC_LANG_RELATED', 'Related');
define('A11YC_LANG_AS', 'Accessibility Supported');
define('A11YC_LANG_UNDERSTANDING', 'Understanding WCAG2.0');
define('A11YC_LANG_NO_DOC', 'There is no document');
define('A11YC_LANG_JUMP_TO_CONTENT', 'Jump to content');
define('A11YC_LANG_BEGINNING_OF_THE_CONTENT', 'Start of main content');
define('A11YC_LANG_UPDATE_SUCCEED', 'Update Succeed');
define('A11YC_LANG_UPDATE_FAILED', 'Update Failed');
define('A11YC_LANG_ERROR_NON_TARGET_LEVEL', 'Please perform the check from select "Target Level" in setting.');
define('A11YC_LANG_CTRL_CONFIRM', 'It can not be returned. Are you sure you want to do %s?');
define('A11YC_LANG_CTRL_KEYWORD_TITLE', 'Keyword');
define('A11YC_LANG_CTRL_ORDER_TITLE', 'Order');
define('A11YC_LANG_CTRL_SEARCH', 'Search');
define('A11YC_LANG_CTRL_SEND', 'Send');
define('A11YC_LANG_CTRL_PREV', 'Previous');
define('A11YC_LANG_CTRL_NEXT', 'Next');
define('A11YC_LANG_CTRL_NUM', 'Number');
define('A11YC_LANG_CTRL_EXPAND', 'Expand');
define('A11YC_LANG_CTRL_COMPRESS', 'Compress');

// login
define('A11YC_LANG_AUTH_TITLE', 'A11YC Login');
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

// pages
define('A11YC_LANG_PAGES_TITLE', 'Target Pages');
define('A11YC_LANG_PAGES_INDEX', 'Target Pages');
define('A11YC_LANG_PAGES_PAGETITLE', 'Target Page\'s title');
define('A11YC_LANG_PAGES_URLS', 'Target Page\'s URL');
define('A11YC_LANG_PAGES_URLS_ADD', 'Add URL');
define('A11YC_LANG_PAGES_NOT_FOUND', 'No pages to check found');
define('A11YC_LANG_PAGES_ALREADY_EXISTS', 'Already exists');
define('A11YC_LANG_PAGES_ADDED_NORMALLY', 'Add');
define('A11YC_LANG_PAGES_ADD_FAILED', 'Failed');
define('A11YC_LANG_PAGES_DONE', 'Done');
define('A11YC_LANG_PAGES_CHECK', 'Check');
define('A11YC_LANG_PAGES_DELETE', 'Delete');
define('A11YC_LANG_PAGES_UNDELETE', 'Undelete');
define('A11YC_LANG_PAGES_PURGE', 'Purge');
define('A11YC_LANG_PAGES_ADD_DATE', 'Add Date');
define('A11YC_LANG_PAGES_ORDER_ADD_DATE_ASC', 'Add Date Asc.');
define('A11YC_LANG_PAGES_ORDER_ADD_DATE_DESC', 'Add Date Desc.');
define('A11YC_LANG_PAGES_ORDER_TEST_DATE_ASC', 'Test Date Asc');
define('A11YC_LANG_PAGES_ORDER_TEST_DATE_DESC', 'Test Date Desc');
define('A11YC_LANG_PAGES_ORDER_URL_ASC', 'URL Asc.');
define('A11YC_LANG_PAGES_ORDER_URL_DESC', 'URL Desc.');
define('A11YC_LANG_PAGES_ORDER_PAGE_NAME_ASC', 'Page name Asc.');
define('A11YC_LANG_PAGES_ORDER_PAGE_NAME_DESC', 'Page name Desc.');
define('A11YC_LANG_PAGES_CTRL', 'Action');
define('A11YC_LANG_PAGES_URL_FOR_EACH_LINE', 'Enter one URL to each line, please press the "'.A11YC_LANG_PAGES_URLS_ADD.'".  Please once the registration is in the order of twenty. The program may
be stopped at the registration process and too many.');
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
define('A11YC_LANG_PAGES_NOT_FOUND_SSL', 'In .htaccess, redirecting access to http to https may not find the link. Please try adding <code>RewriteCond %{QUERY_STRING} !jwp-a11y=ssl</code> line after <code>RewriteEngine On</code> of .htaccess.');
define('A11YC_LANG_PAGES_ADD_TO_DATABASE', 'Register the URLs in the database');
define('A11YC_LANG_PAGES_ADD_TO_CANDIDATE', 'Acquire candidate URLs from HTML');
define('A11YC_LANG_PAGES_IT_TAKES_TIME', 'This process takes time.');

// setup
define('A11YC_LANG_SETUP_TITLE', 'Setup');
define('A11YC_LANG_SETUP_TITLE_ETC', 'etc.');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR', 'Checklist Behaviour');
define('A11YC_LANG_SETUP_CHECKLIST_BEHAVIOUR_DISAPPEAR', 'Disappear when check');
define('A11YC_LANG_SETUP_BASIC_AUTH_TITLE', 'Basic Auth');
define('A11YC_LANG_SETUP_BASIC_AUTH_EXP', 'If the site to be tested is protected by basic authentication, please enter the user name and password for basic authentication here.');
define('A11YC_LANG_SETUP_BASIC_AUTH_USER', 'Basic Auth user');
define('A11YC_LANG_SETUP_BASIC_AUTH_PASS', 'Basic Auth password');
define('A11YC_LANG_SETUP_TRUST_SSL_TITLE', 'SSL');
define('A11YC_LANG_SETUP_TRUST_SSL_EXP', 'When targeting SSL sites, please enter that domain here. Please do not enter URL of untrusted site. (ex: www.example.com)');

define('A11YC_LANG_DECLARE_DATE', 'Declare Date');
define('A11YC_LANG_STANDARD', 'Standard');
define('A11YC_LANG_DEPENDENCIES', 'List of dependent Web content technology');
define('A11YC_LANG_TEST_PERIOD', 'Test Period');
define('A11YC_LANG_TEST_DATE', 'Test Date');
define('A11YC_LANG_TARGET_LEVEL', 'Target Level');
define('A11YC_LANG_POLICY', 'Accessibility Policy');
define('A11YC_LANG_POLICY_DESC', 'Why you work in web accessibility ensure, goal date, corresponding policies, exceptions, please write and achieve grade to which you want to add to goal.');
define('A11YC_LANG_REPORT', 'Accessibility Report');
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
define('A11YC_LANG_CHECKLIST_NOT_FOUND_ERR', 'Could not find an error in the automatic check');
define('A11YC_LANG_CHECKLIST_TITLE', 'Checklist');
define('A11YC_LANG_CHECKLIST_DONE', 'Done');
define('A11YC_LANG_CHECKLIST_RESTOFNUM', 'Rest Of Numbers');
define('A11YC_LANG_CHECKLIST_TOTAL', 'Total');
define('A11YC_LANG_CHECKLIST_ACHIEVEMENT', 'Achieve Grade');
define('A11YC_LANG_CHECKLIST_CONFORMANCE', 'Conformance with %s');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_PARTIAL', 'Partial Conformance with %s');
define('A11YC_LANG_CHECKLIST_CONFORMANCE_ADDITIONAL', 'Additional Conformance');
define('A11YC_LANG_CHECKLIST_NON_INTERFERENCE', 'Non interference');
define('A11YC_LANG_CHECKLIST_DO_LINK_CHECK', 'Do link check');
define('A11YC_LANG_CHECKLIST_VIEW_SOURCE', 'View source code');
define('A11YC_LANG_CHECKLIST_MACHINE_CHECK', 'Automatic Check');
define('A11YC_LANG_CHECKLIST_MEMO', 'Note');
define('A11YC_LANG_NO_BROKEN_LINK_FOUND', 'No broken link was found');
define('A11YC_LANG_CHECKLIST_PERCENTAGE', 'Achievement');
define('A11YC_LANG_CHECKLIST_NG_REASON', 'Nonconformity Reason');
define('A11YC_LANG_CHECKLIST_NG_REASON_EXP', 'If there is application of the object of this achievement item and it is nonconformity, please describe the reason here. If the reason is
written here, this criterion will be treated as "Nonconformity" or "Partial Conformity".');

// bulk
define('A11YC_LANG_BULK_TITLE', 'Batch');
define('A11YC_LANG_BULK_UPDATE', 'Batch Type');
define('A11YC_LANG_BULK_UPDATE1', 'Save as the initial value at the new check (does not change any of the test results of an existing page)');
define('A11YC_LANG_BULK_UPDATE2', 'Only those that checked reflected in the existing test results (but does not update to remove the check)');
//define('A11YC_LANG_BULK_UPDATE3', 'Remove the check is also reflected in the existing test results (Note: The test results of all the pages will be the same in the site)');
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
