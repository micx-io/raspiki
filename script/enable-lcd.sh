#!/bin/bash


cp -R /home/user/LCD_driver /host/tmp/


sudo chroot /host cd /tmp/LCD_driver/ &&/tmp/LCD_driver/LCD35_show
