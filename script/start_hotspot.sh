#!/bin/bash

set -e -x

WIFI_IF=$(iwconfig 2>&1 | grep IEEE | awk '{print $1;}')
WIFI_CHANNEL=$(iwlist channel 2> /dev/null | awk '/Current/ {print substr($5,1,length($5) - 1)}')

AP_IF=uae0

cat >/etc/hostapd/hostapd.conf <<EOF

interface=$AP_IF
ssid=raspiki_7Z4F5
driver=nl80211
#hw_mode=g
ieee80211n=1
channel=$WIFI_CHANNEL
macaddr_acl=0
auth_algs=1
ignore_broadcast_ssid=0
#wpa=2
ctrl_interface=/var/run/hostapd
ctrl_interface_group=0
#wpa_passphrase=yourAPpsk
#wpa_key_mgmt=WPA-PSK
#rsn_pairwise=CCMP

EOF


cat > /etc/dnsmasq.conf <<EOF

interface=lo,$AP_IF
no-dhcp-interface=lo,wlan0
bind-interfaces
server=8.8.8.8
domain-needed
bogus-priv
dhcp-range=192.168.69.50,192.168.69.150,12h

EOF

cat > /etc/dhcpcd.conf <<EOF

interface $AP_IF

EOF


iw dev $WIFI_IF interface add $AP_IF type __ap
#ifdown $WIFI_IF

ip link set dev $AP_IF up
ip addr add 192.168.69.1/24 broadcast 192.168.69.255 dev $AP_IF
sleep 5

hostapd -B -P /run/hostapd.pid /etc/hostapd/hostapd.conf &

sleep 5
service hostapd restart

#ifup $WIFI_IF
service dnsmasq restart
