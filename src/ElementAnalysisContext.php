<?php

namespace Jidaikobo\A11yc;

class ElementAnalysisContext
{
    public $sourceHtml = '';
    public $ignoredHtml;
    public $lang;
    public $langResolved = false;
    public $elementMatches = array();
    public $attributes = array();

    public function __construct()
    {
        $this->ignoredHtml = null;
        $this->lang = null;
    }
}
