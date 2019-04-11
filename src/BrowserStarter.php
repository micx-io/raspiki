<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 11.04.19
 * Time: 13:07
 */

namespace Micx\Raspiki;


class BrowserStarter
{


    public function open(string $url)
    {
        putenv("DISPLAY=" . DISPLAY);

        $pidfile = phore_file("/tmp/chrome.pid");
        if ($pidfile->exists()) {
            try {
                phore_exec("kill ?", [$pidfile->get_contents()]);
            } catch (\Phore\System\PhoreExecException $ex) {

            }
        }

        $pid = phore_exec("chromium-browser --incognito --app=?  > /dev/null & echo \$!;", [$url]);
        $pidfile->set_contents($pid);
    }


}
