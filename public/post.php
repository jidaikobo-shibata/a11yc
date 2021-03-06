<?php
/**
 * A11yc Online Validation
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// controller
require (dirname(__DIR__).'/classes/Controller/PostAuth.php');
require (dirname(__DIR__).'/classes/Controller/Post.php');
\A11yc\Controller\Post::forge();
