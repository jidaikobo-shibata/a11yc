<?php
/**
 * A11yc API
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 */

// controller
require (dirname(__DIR__).'/libs/a11yc/classes/Api.php');
echo \A11yc\Api::forge();
