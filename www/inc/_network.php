<?php

namespace App;
use Micx\Raspiki\HostConfig;
use Phore\StatusPage\PageHandler\NaviButtonWithIcon;

$app->addPage("/network", function ()  {

    $hostConfig = new HostConfig();
    $wifiIf = $hostConfig->getWirelessInterface();

    $e = fhtml("div @row");

    $ipTbl = phore_array_transform($hostConfig->getIpA(), function ($key, $val) {
        $ip = phore_pluck("inet_addr_primary.local", $val, "");
        $prefix = phore_pluck("inet_addr_primary.prefixlen", $val, "");

        return [$val["ifname"], $ip . " /" . $prefix, $val["operstate"]];
    });

    $colLeft = $e["div @col-8"];

    $colLeft[]= pt()->card(
        "Network connections",
        pt()->basic_table(
            ["Interface", "IP", "Status"],
            $ipTbl
        )
    );

    $colLeft[]= pt()->card(
        "Connect to wifi network",
        [

        ]
    );

    $tbl = [];
    if ($wifiIf !== null) {
        $nets = $hostConfig->getWirelessNetworks($wifiIf);
        //print_r ($nets);
        $tbl = phore_array_transform($nets, function ($key, $value) {
            return [$value["ESSID"], $value["Quality"], $value["Signal level"]];
        });
    }




    $e["div @col-4"] = pt()->card(
        $wifiIf !== null ? "Available Wifi Networks ($wifiIf)" : "No wifi adapters found!",
        [
            pt()->basic_table(
                ["SSID", "Quality", "Strength"],
                $tbl

            )
        ]
    );

    return $e;

}, new NaviButtonWithIcon("Network", "fas fa-wifi"));
