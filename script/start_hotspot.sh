#!/bin/bash

set -e -x

WIFI_IF=$(iwconfig 2>&1 | grep IEEE | awk '{print $1;}')
WIFI_CHANNEL=$(iwlist channel 2> /dev/null | awk '/Current/ {print substr($5,1,length($5) - 1)}')

AP_IF=uae0

cat >/etc/hostapd/hostapd.conf <<EOF

interface=$AP_IF
ssid=raspiki_7Z4F5
driver=nl80211

channel=$WIFI_CHANNEL
#channel=6
macaddr_acl=0
auth_algs=1
ignore_broadcast_ssid=0
wpa=0
#ctrl_interface=/var/run/hostapd
#ctrl_interface_group=0
country_code=DE
#wpa_passphrase=
#wpa_key_mgmt=WPA-PSK
rsn_pairwise=CCMP
#ht_capab=[HT40-][SHORT-GI-20][SHORT-GI-40]


## Config
hw_mode=a
#wmm_enabled=1

# N
ieee80211n=1
#require_ht=1
#ht_capab=[MAX-AMSDU-3839][HT40+][SHORT-GI-20][SHORT-GI-40][DSSS_CCK-40]

# AC
ieee80211ac=1
require_vht=1
ieee80211d=1
ieee80211h=1
#vht_capab=[MAX-AMSDU-3839][SHORT-GI-80]
vht_oper_chwidth=1
vht_oper_centr_freq_seg0_idx=42

EOF


cat > /etc/dnsmasq.conf <<EOF

interface=$AP_IF
#no-dhcp-interface=lo,wlan0
server=8.8.8.8
domain-needed
bogus-priv
dhcp-range=192.168.0.50,192.168.0.150,12h
dhcp-option=3,192.168.0.1
EOF

cat > /etc/dhcpcd.conf <<EOF

interface $AP_IF

EOF

# Allow creating AP >channel 100
#iw reg set DE

ip link set dev $WIFI_IF down
iw dev $WIFI_IF interface add $AP_IF type __ap
#


ip addr add 192.168.0.1/24 broadcast 192.168.0.255 dev $AP_IF
ip link set dev $WIFI_IF up
ip link set dev $AP_IF up
sleep 5

hostapd -dd -B -P /run/hostapd.pid /etc/hostapd/hostapd.conf &

sleep 5
#service hostapd restart

#service hostpad stop
service dnsmasq stop
#ifup $WIFI_IF
dnsmasq -dd &
