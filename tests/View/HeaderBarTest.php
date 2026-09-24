<?php

namespace Tests\View;

use Tests\TestCase;

class HeaderBarTest extends TestCase
{
    public function test_header_logo_alt_tag_blank_when_used_with_app_name()
    {
        $this->setSettings([
            'app-name' => 'Dino Docs',
            'app-logo' => '',
            'app-name-header' => true,
        ]);

        $resp = $this->get('/login');
        $this->withHtml($resp)->assertElementExists('header img.logo-image[alt=""]');
    }

    public function test_header_logo_alt_tag_uses_app_name_when_name_not_shown_in_header()
    {
        $this->setSettings([
            'app-name' => 'Dino Docs',
            'app-logo' => '',
            'app-name-header' => false,
        ]);

        $resp = $this->get('/login');
        $this->withHtml($resp)->assertElementExists('header img.logo-image[alt="Dino Docs"]');
    }
}
