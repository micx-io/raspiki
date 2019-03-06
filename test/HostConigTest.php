<?php


namespace Test;


use Micx\Raspiki\HostConfig;
use PHPUnit\Framework\TestCase;

class HostConigTest extends TestCase
{


    public function testIwListParsing()
    {
        $hc = new HostConfig();
        $out = $hc->getWirelessNetworks("xxx", file_get_contents(__DIR__ . "/iwlist.out.txt"));
        print_r ($out);

        $this->assertEquals(10, count($out));
    }


    public function testConnectNetwork()
    {
        $hc = new HostConfig();
        $hc->connectNetwork("wlp2s0", "demo1", "", true);
    }

}
