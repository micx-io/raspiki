<?php

use Micx\Raspiki\HostConfig;
use Phore\StatusPage\PageHandler\NaviButtonWithIcon;

$app->addPage("/view", function ()  {
    $e = fhtml("div @row");

    phore_exec("chromium-browser");
    $e[]= pt()->card(
        "Connections",
        pt()->basic_table(
            ["Interface", "IP", "Status"],
            [

            ]
        )
    );
    return $e;

}, new NaviButtonWithIcon("View", "fas fa-wifi"));
