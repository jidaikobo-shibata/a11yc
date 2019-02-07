<?php
/**
 * A11yc\Model\Data
 *
 * @package    part of A11yc
 * @author     Jidaikobo Inc.
 * @license    The MIT License (MIT)
 * @copyright  Jidaikobo Inc.
 * @link       http://www.jidaikobo.com
 *
 * key field:
 * - setting
 * - page
 * - check
 * - result
 * - issue
 * - html
 * - icl
 * - iclsit
 * - iclchk
 * - vesion
 *
 * url field:
 * - url is specified url
 * - "common" is non specified url. used by issue, setting and version.
 * - "global" whole system setting. this key's group_id must be 1.
 */
namespace A11yc\Model;

class Data
{
	use DataFetch;
	use DataUpdate;
	use DataFilter;
}
