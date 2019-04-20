#!/bin/bash
filename="`date '+%m%d-%H%M'`.sfs3.pinyin.modules.tar.gz"
cd /var/www/sfs3/modules
tar zcvf ./$filename ./pinyin
mv ./$filename /mnt/img/sfs3bkup/pinyin/
