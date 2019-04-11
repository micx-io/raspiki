<?php

use Micx\Raspiki\HostConfig;
use Phore\StatusPage\PageHandler\NaviButtonWithIcon;

$app->addPage("/network", function ()  {
    $interface = phore_exec("iwconfig 2>&1 | grep IEEE | awk '{print $1;}'", []);
    $hostConfig = new HostConfig();
    $e = fhtml("div @row");

    $ipTbl = phore_array_transform($hostConfig->getIpA(), function ($key, $val) {
        $i =  ["local" => "", "label"=>"undefinded", "operstate" => "DOWN"];
        if (isset ($val["addr_info"][0]))
            $i = $val["addr_info"][0];
        return [$i["label"], $i["local"], $val["operstate"]];
    });

    $colLeft = $e["div @col-8"];

    $colLeft[]= pt()->card(
        "Connections",
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


    $nets = $hostConfig->getWirelessNetworks($interface);
    //print_r ($nets);
    $tbl = phore_array_transform($nets, function ($key, $value) {
        return  [$value["ESSID"], $value["Quality"], $value["Signal level"]];
    });

    $e["div @col-4"] = pt()->card(
        "Available Wifi Networks",
        [
            pt()->basic_table(
                ["SSID", "Quality", "Strength"],
                $tbl

            )
        ]
    );

    return $e;

}, new NaviButtonWithIcon("Network", "fas fa-wifi"));
