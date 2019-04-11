<?php

namespace App;

use Micx\Raspiki\BrowserStarter;
use Micx\Raspiki\HostConfig;
use Phore\StatusPage\PageHandler\NaviButtonWithIcon;

$app->addPage("/view", function ()  {
    $e = fhtml("div @row");

    $browser = new BrowserStarter();
    $browser->open("http://google.de");

    $e[]= pt()->card(
        "Connections",
       "muh"
    );
    return $e;

}, new NaviButtonWithIcon("View", "fas fa-wifi"));
