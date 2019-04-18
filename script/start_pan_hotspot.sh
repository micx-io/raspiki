#!/bin/bash

set -e
AP_IF=pan0


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


## Set the discoverable name
echo "PRETTY_HOSTNAME=raspiki-X74B3" > /host/etc/machine-info


## Set the bluetooth name
hciconfig hci0 name "raspiki_x74B"

bt-agent -c NoInputNoOutput &

ip link add pan0 type veth
ip addr add 192.168.0.1/24 broadcast 192.168.0.255 dev pan0
dnsmasq -dd &

bt-network -s server pan0 &
bt-adapter --set Discoverable 1
