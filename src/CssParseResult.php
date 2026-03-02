<?php

namespace Jidaikobo\A11yc;

class CssParseResult
{
    public $csses = array();
    public $is_suspicious_paren_num = false;
    public $suspicious_prop_and_vals = array();
    public $suspicious_props = array();
    public $suspicious_val_prop = array();
}
