#!/bin/bash

set -e -x

curl -o /tmp/lcd.zip http://kedei.net/raspberry/spi_128M/LCD_driver.zip
unzip /tmp/lcd.zip -d /home/user

