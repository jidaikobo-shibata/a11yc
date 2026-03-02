<?php

namespace Jidaikobo\A11yc;

final class ValidationContext
{
    public $userAgent = 'using';
    public $isPartial = false;
    public $doLinkCheck = false;
    public $doCssCheck = false;
    public $errorIds = array();
    public $machineChecks = array();
    public $errCnts = array(
        'a' => 0,
        'aa' => 0,
        'aaa' => 0,
    );
}
