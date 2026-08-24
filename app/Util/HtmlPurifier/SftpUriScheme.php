<?php

namespace BookStack\Util\HtmlPurifier;

use HTMLPurifier_URIScheme_ftp;

class SftpUriScheme extends HTMLPurifier_URIScheme_ftp
{
    public $default_port = 22;
}
