
DESCRIPTION

MARC-XML / OAI-PMH toolkit.

INSTALL
<pre>
# EXTRA

net-tools tcpdump mc

# POSTFIX

apt-get install postfix

"Satellite system"

xxx [relay]

/etc/aliases:

webmaster: root

postalias /etc/aliases
postfix reload

# SSL

cd /usr/local/share/ca-certificates
wget -O TERENA_SSL_CA_3.crt https://pki.cesnet.cz/certs/TERENA_SSL_CA_3.pem
update-ca-certificates

apt-get install certbot
certbot certonly -d xxx

# GIT

apt-get install git

ssh-kegen -t ed25519

# DAVFS

apt-get install davfs2

usermod -a -G davfs2 xxx

/etc/davfs2/davfs2.conf:

use_locks	0

/etc/davfs2/secrets:

/home/xxx xxx xxx

/etc/fstab:

https://xxx/public.php/webdav/ /home/xxx davfs rw,user 0 0

# NGINX

apt-get install nginx php7.3 php7.3-fpm php7.3-cli php7-gd php7-ldap php7.3-sqlite3

mkdir -p /var/www/html/.well-known/acme-challenge
chown -R www-data:www-data /var/www/html/.well-known

# MARC

field.patch

# OAI

apt-get install python3-lxml python3-six

pyoai.patch

# API

apt-get install python3-flask python3-restful

cp api /usr/locail/bin
ln -s /usr/local/bin/api/vufind-api vufind-api
ln -s /usr/local/bin/api/vufind-update vufind-update
cp /usr/local/bin/api/vufind-api.service /etc/systemd/system/

systemctl enable vufind-api.service

# CRON
crontab -e
0 1     * * *   cd ~/UCL && git add . && git commit -m "Git auto backup." && git push origin master >> ~/git.log 2>&1 &
</pre>
FILE
<pre>
        oai-marc - OAI-PMH 2.0 MARCXML record validation.
         oai-mdt - Z39.50 yaz-client wrapper.
         oai-mod - Modular OAI-PMH loader.
     oai-recenze - Subset match notify.

            mod/
            five - Send dumped 245/246/5xx text for correction. 
           seven - Evaluate subfield "7" data. 
             kat - Evaluate field "KAT" data. 

           patch/
  field.py.patch - Allow non-standard control field(FMT).
     pyoai.patch - Python3 test file patch.

           code/
     country.txt - MARC country code file.
        lang.txt - MARC language code file.
        role.txt - MARC role code file.
         sif.txt - MARC sif code file.
     recenze.csv - Data file oai-recenze.

            api/ - Flask REST API.
           cron/ - Cron scheduling.
           html/ - PHP7 Website.
            xml/ - XML file parsing.
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

