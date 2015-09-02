<?php

class AuthTest extends TestCase
{

    public function testAuthWorking()
    {
        $this->visit('/')
            ->seePageIs('/login');
    }

    public function testLogin()
    {
        $this->visit('/')
            ->seePageIs('/login')
            ->type('admin@admin.com', '#email')
            ->type('password', '#password')
            ->press('Sign In')
            ->seePageIs('/')
            ->see('BookStack');
    }

    public function testLogout()
    {
        $this->asAdmin()
            ->visit('/')
            ->seePageIs('/')
            ->visit('/logout')
            ->visit('/')
            ->seePageIs('/login');
    }
}
