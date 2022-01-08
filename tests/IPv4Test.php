<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Subb98\IPv4Utils\IPv4;

final class IPv4Test extends TestCase
{
    public function testGetBinary(): void
    {
        $ip = new IPv4('192.168.0.0');
        $this->assertEquals("11000000.10101000.00000000.00000000", $ip->getBinary());
    }

    public function testGetClass(): void
    {
        $ip = new IPv4('192.168.0.0');
        $this->assertEquals("C", $ip->getClass());
    }

    public function testGetDefaultSubnetMask(): void
    {
        $ip = new IPv4('192.168.0.0');
        $this->assertEquals("255.255.255.0", $ip->getDefaultSubnetMask());
    }

    public function testGetClassfulSubnet(): void
    {
        $ip = new IPv4('192.168.0.0');
        $result = [
            'class' => 'C',
            'network_bits' => 24,
            'subnets_bits' => 2,
            'hosts_bits' => 6,
            'subnet_mask_binary' => '11111111.11111111.11111111.11000000',
            'subnet_mask_decimal' => '255.255.255.192',
            'subnet_mask_short' => '192.168.0.0/26',
        ];
        $this->assertEquals($result, $ip->getClassfulSubnet(4, 60));
    }
}
