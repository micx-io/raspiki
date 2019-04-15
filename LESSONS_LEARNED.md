## Start XWidnows app from within container

exec("DISPLAY=:0 chromium --incognito --app=http://google.de --kiosk");


## Make AP and Wifi

https://www.raspberrypi.org/forums/viewtopic.php?t=196263


## Channel selection failed

```
sudo iw list
```

shows `no IR` behind channel: This channel cannot be used
to initiate hostap due to hardware country restrictions

https://github.com/raspberrypi/linux/issues/2619#issuecomment-410703338

## Resetting interfaces

sudo ip link set uae0 down
sudo ip link set uae0 up
hostapd -dd -P


## Start network

wpa_supplicant  -Dnl80211 -iwlan0 -c/etc/wpa_supplicant.conf -dd
