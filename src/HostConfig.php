<?php


namespace Micx\Raspiki;


class HostConfig
{

    public function getWirelessInterface() : ?string
    {
        $ret = phore_exec("iwconfig 2>&1 | grep IEEE | awk '{print $1;}'", []);
        if (trim ($ret) === "")
            return null;
        return $ret;
    }

    public function getWirelessNetworks(string $device="wlp2s0", string $iwoutput=null)
    {
        if ($iwoutput === null)
            $iwoutput = phore_exec("sudo iwlist :device scanning", ["device"=>$device]);
        $blocks = preg_split("/\\n\\s*Cell [0-9]+ \- /im", $iwoutput);

        $list = [];
        foreach ($blocks as $curBlock) {
            $parsed = ["Encryption" => []];
            preg_replace_callback("/^\s*([a-z0-9 ]+):\\s*\"?(.*?)\"?$/im", function ($matches) use (&$parsed) {
                if (in_array($matches[1], ["IE"]))
                    return;
                 $parsed[$matches[1]] = $matches[2];
            }, $curBlock);
            preg_replace_callback("/^\s*Quality=([0-9\/]+)\s+Signal level=([0-9\-]+) dBm/im", function ($matches) use (&$parsed) {
                $parsed["Quality"] = $matches[1];
                $parsed["Signal level"] = $matches[2];
            }, $curBlock);
            preg_replace_callback("/^\s*IE: (.*)$/im", function ($matches) use (&$parsed) {
                if($matches[1] == "WPA Version 1")
                    $parsed["Encryption"][] = "WPA";

                if($matches[1] == "IEEE 802.11i/WPA2 Version 1")
                    $parsed["Encryption"][] = "WPA2";

            }, $curBlock);

            if (isset($parsed["Address"]) && isset($parsed["ESSID"]) && isset($parsed["Quality"])) {
                $list[] = $parsed;
            }
        }
        return $list;
    }


    public function getIpA () {
        $data = json_decode(phore_exec("ip -j a"), true);

        $data = phore_array_transform($data, function($key, $data) {
            try {
                $r = [
                    "ifname" => phore_pluck("ifname", $data),
                    "operstate" => phore_pluck("operstate", $data),
                    "link_type" => phore_pluck("link_type", $data),
                    "address" => phore_pluck("address", $data),
                    "inet_addr_primary" => null,
                    "inet6_addr_primary" => null,
                    "inet_addr" => [],
                    "inet6_addr" => []
                ];

                foreach (phore_pluck("addr_info", $data, []) as $addrData) {
                    try {
                        $retData = [
                            "family" => phore_pluck("family", $addrData),
                            "local" => phore_pluck("local", $addrData),
                            "prefixlen" => phore_pluck("prefixlen", $addrData),
                            "label" => phore_pluck("label", $addrData, null),
                            "broadcast" => phore_pluck("label", $addrData, null)
                        ];
                        if ($retData["family"] === "inet") {
                            if ($r["inet_addr_primary"] === null)
                                $r["inet_addr_primary"] = $retData;
                            $r["inet_addr"][] = $retData;
                        } else if ($retData["family"] === "inet6") {
                            if ($r["inet6_addr_primary"] === null)
                                $r["inet6_addr_primary"] = $retData;
                            $r["inet6_addr"][] = $retData;
                        }
                    } catch (\Exception $e) {
                        throw $e;
                        continue;
                    }
                }
                return $r;

            } catch (\Exception $e) {
                throw $e;
                return null;
            }

        });
        return $data;
    }


    public function connectNetwork ($device, $ssid, $psk, $dhcp=true) {
        file_put_contents("/host/tmp/wpa_supplicant.conf", "network={\nssid=\"$ssid\"\npsk=\"$psk\"\n}");

        //echo phore_exec("sudo chroot /host systemctl reenable wpa_supplicant.service");
        //echo phore_exec("sudo chroot /host systemctl restart wpa_supplicant.service");
        //phore_exec("sudo wpa_supplicant -u -s -O /run/wpa_supplicant");

        $pass = "ctrl_interface=/run/wpa_supplicant\nupdate_config=1\n";
        $pass .= phore_exec("sudo wpa_passphrase $ssid $psk");
        file_put_contents("/tmp/wpa.conf", $pass);

        echo phore_exec("sudo wpa_supplicant -B -i$device -c/tmp/wpa.conf -Dwext -dd");

        if ($dhcp === true)
            echo phore_exec("sudo chroot /host dhclient $device");
    }

}
