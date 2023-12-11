<?php

namespace Tests\Unit;

use BookStack\Activity\Tools\IpFormatter;
use Tests\TestCase;

class IpFormatterTest extends TestCase
{
    public function test_ips_formatted_as_expected()
    {
        $this->assertEquals('192.123.45.5', (new IpFormatter('192.123.45.5', 4))->format());
        $this->assertEquals('192.123.45.x', (new IpFormatter('192.123.45.5', 3))->format());
        $this->assertEquals('192.123.x.x', (new IpFormatter('192.123.45.5', 2))->format());
        $this->assertEquals('192.x.x.x', (new IpFormatter('192.123.45.5', 1))->format());
        $this->assertEquals('x.x.x.x', (new IpFormatter('192.123.45.5', 0))->format());

        $ipv6 = '2001:db8:85a3:8d3:1319:8a2e:370:7348';
        $this->assertEquals($ipv6, (new IpFormatter($ipv6, 4))->format());
        $this->assertEquals('2001:db8:85a3:8d3:1319:8a2e:x:x', (new IpFormatter($ipv6, 3))->format());
        $this->assertEquals('2001:db8:85a3:8d3:x:x:x:x', (new IpFormatter($ipv6, 2))->format());
        $this->assertEquals('2001:db8:x:x:x:x:x:x', (new IpFormatter($ipv6, 1))->format());
        $this->assertEquals('x:x:x:x:x:x:x:x', (new IpFormatter($ipv6, 0))->format());
    }

    public function test_shortened_ipv6_addresses_expands_as_expected()
    {
        $this->assertEquals('2001:0:0:0:0:0:x:x', (new IpFormatter('2001::370:7348', 3))->format());
        $this->assertEquals('2001:0:0:0:0:85a3:x:x', (new IpFormatter('2001::85a3:370:7348', 3))->format());
        $this->assertEquals('2001:0:x:x:x:x:x:x', (new IpFormatter('2001::', 1))->format());
    }
}
