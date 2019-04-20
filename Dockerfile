FROM liuchenwei/docker-nginx-php53

COPY ./sfs_stable5/sfs3_stable/ /var/www/app/
WORKDIR /var/www/app/

