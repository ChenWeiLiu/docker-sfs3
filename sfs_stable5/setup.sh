#! /bin/sh

clear &&

echo &&
echo "Mmmmmmmmmmm..... Let's do something for u ...." &&
echo &&
sleep 1 &&

echo 'Checking sfs3 directory in your working dir ....' &&
echo &&
sleep 1 &&

if [ -e sfs3  ]; then
        echo 'Oh! Error! .... File *** sfs3 *** exists!'
        echo 'Please remove it first! And run ./setup.sh again!'
		echo
        exit
fi

echo 'Great! wait ......' &&
echo &&
sleep 1 &&

VER=sfs3_stable &&

mv $VER sfs3 &&
echo "Rename $VER ----> sfs3" &&
echo &&
sleep 1 &&

mkdir -p sfs3/data &&
echo 'mkdir -p sfs3/data' &&
echo &&
sleep 1 &&

chmod 777 sfs3/data &&
echo 'chmod 777 sfs3/data' &&
echo &&
sleep 1 &&

touch sfs3/include/config.php &&
chmod 666 sfs3/include/config.php &&
echo 'chmod 666 sfs3/include/config.php' &&
echo &&
sleep 1 &&

echo 'Done!'
echo &&
sleep 1 &&

echo 'Now you can run:' &&
echo '-----------------------------------------------' &&
echo "http://`hostname`/sfs3/install.php " &&
echo '-----------------------------------------------' &&
echo 'in your browser!' &&
echo &&
sleep 1 &&

echo 'Thanks for using SFS project.  The SFS group' &&
echo '                               http://sfs.wpes.tcc.edu.tw' &&
echo '                               http://cvs.tnc.edu.tw' &&
echo

