
DESCRIPTION

MARC-XML / OAI-PMH toolkit.

INSTALL
<pre>
# EXTRA

net-tools tcpdump cadaver git mc

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
certbot certonly --standalone -d xxx
/etc/letsencrypt/cli.ini:
deploy-hook = systemctl reload nginx
/etc/letsencrypt/options-ssl-nginx.conf:
https://github.com/certbot/certbot/blob/master/certbot-nginx/certbot_nginx/_internal/tls_configs/options-ssl-nginx.conf

# GIT

apt-get install git

ssh-kegen -t ed25519

# DAVFS

apt-get install python3-easywebdav
apt-get install davfs2

usermod -a -G davfs2 xxx

/etc/davfs2/davfs2.conf:

use_locks	0

/etc/davfs2/secrets:

/home/xxx xxx xxx

/etc/fstab:

https://xxx/public.php/webdav/ /home/xxx davfs rw,user 0 0

# MARC

(pymarc-4.0.0)

field.patch

# OAI

apt-get install python3-lxml python3-six

(pyoai-2.5.0)

pyoai.patch

# CRON

/etc/crontab:
10 *    * * *   root    oai-hourly >> /var/log/oai-hourly.log 2>&1 &
20 *	* * *	root    solr-update >> /var/log/solr-update.log 2>&1 &
00 5    * * *   root    oai-daily >> /var/log/oai-daily.log 2>&1 &
00 6    * * TUE root    oai-weekly >> /var/log/oai-weekly.log 2>&1 &
00 7    1 * *   root    oai-monthly >> /var/log/oai-monthly.log 2>&1 &

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
     recenze.txt - Data file oai-recenze.
         kat.txt - Aleph editor SIF.

           cron/ - Cron scheduling.
            xml/ - XML file parsing.
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

