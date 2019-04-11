<?php

namespace App;
use Micx\Raspiki\HostConfig;
use Phore\StatusPage\PageHandler\NaviButtonWithIcon;
use Phore\StatusPage\StatusPageApp;


require __DIR__ . "/../vendor/autoload.php";

$app = new StatusPageApp("raspiki");

$app->addPage("/", function ()  {

}, new NaviButtonWithIcon("Status", "icon"));


require __DIR__ . "/inc/_network.php";
require __DIR__ . "/inc/_view.php";
/**
 ** Run the application
 **/
$app->serve();
