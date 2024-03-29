DESCRIPTION

Vufind server howto.

TODO
<pre>
-Czech analyzer:
https://solr.apache.org/guide/8_0/schema-api.html
&lt;fieldType name="text_cz" class="solr.TextField" positionIncrementGap="100"&gt;
    &lt;analyzer&gt;
      &lt;tokenizer class="solr.StandardTokenizerFactory"/&gt;
      &lt;filter class="solr.LowerCaseFilterFactory"/&gt;
      &lt;filter class="solr.StopFilterFactory" words="lang/stopwords_cz.txt" ignoreCase="true"/&gt;
      &lt;filter class="solr.CzechStemFilterFactory"/&gt;
    &lt;/analyzer&gt;
  &lt;/fieldType&gt;
</pre>
INSTALL
<pre>
# EXTRA

mc vim net-tools lynx

# NGINX

nginx

# SQL

mariadb-server

# PHP7

php php-fpm php-mbstring php-pear php-gd php-intl php-json php-ldap php-mysql php-xml php-soap php-curl

# CSS

node-less

# JAVA

openjdk-11-jre-headless

# PYMARC

python3-setuptools

pymarc-4.0.0.tar.gz

filed.py.patch (https://github.com/KyomaHooin/UCL/blob/master/oai-marc/patch/field.py.patch)

# CERTBOT

certbot python3-certbot-nginx

certbot certonly --standalone -d xxx
/etc/letsencrypt/cli.ini:
deploy-hook = systemctl reload nginx
/etc/letsencrypt/options-ssl-nginx.conf:
https://github.com/certbot/certbot/blob/master/certbot-nginx/certbot_nginx/_internal/tls_configs/options-ssl-nginx.conf

# AWSTATS

awstats fcgiwrap

/etc/awstats/awstats.conf.local:
LogFile="/var/log/nginx/access.log"
SiteDomain="xxx"
DNSLookup=1

/etc/awstats/awstats.archive.conf:
Include "/etc/awstats/awstats.conf.local"
LogFormat=4
DirData="/var/lib/awstats/archive"
DirIcons="/awstats-icon"

/etc/awstats/awstats.vufind.conf:
Include "/etc/awstats/awstats.conf.local"
LogFormat=4
DirData="/var/lib/awstats"
DirIcons="/awstats-icon"

/var/lib/awstasts/archive:
awstats012020.archive.txt
...

/etc/cron.d/awstats:
#*/10 * * * * www-data [ -x /usr/share/awstats/tools/update.sh ] && /usr/share/awstats/tools/update.sh
#10 03 * * * www-data [ -x /usr/share/awstats/tools/buildstatic.sh ] && /usr/share/awstats/tools/buildstatic.sh

/etc/logrotate.d/httpd-prerotate/awstats:
#!/bin/sh
#UPDATE_SCRIPT=/usr/share/awstats/tools/update.sh
#if [ -x $UPDATE_SCRIPT ]
#then
#  su -s /bin/sh -l -c $UPDATE_SCRIPT www-data
#fi

# INSTALL

cp -r 4xx/ /usr/share/nginx/html/

/etc/ngixnx/nginx.conf:
server_tokens off;

/etc/nginx/sites-enabled/default:

server {
	listen 127.0.0.1:42;

	access_log /var/log/nginx/awstats_access.log combined;
	error_log /var/log/nginx/awstats_error.log;

	root /usr/share/awstats/;
	index /awstats.pl;

	location ~ \.pl$ {
		fastcgi_param SCRIPT_FILENAME /usr/lib/cgi-bin/awstats.pl;
		include fastcgi_params;
		fastcgi_pass unix:/var/run/fcgiwrap.socket;
	}
}

server {
	#listen 80;
	listen 443 ssl;

	add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
	#add_header X-Frame-Options "DENY";
	#add_header X-Robots-Tag "noindex, nofollow, nosnippet, noarchive";

	#ssl_certificate /etc/letsencrypt/live/xxx/fullchain.pem;
	#ssl_certificate_key /etc/letsencrypt/live/xxx/privkey.pem;
	include /etc/letsencrypt/options-ssl-nginx.conf;

	server_name xxx;

	error_page 403 /4xx/403.html;
	error_page 404 /4xx/404.html;

	# 4xx
	location ~ /4xx {
		allow all;
	}

	# SOLR
	location ~ ^/solr {
		allow xxx/24;
		allow 127.0.0.1;
		deny all;
		proxy_pass http://127.0.0.1:8983;
		access_log off;
	}

        # SAM
	location ~ /SAM {
		alias /var/www/html/opvvvweb/www/;
		index index.php;

		try_files $uri $uri/ /SAM/index.php$is_args$args;

		location ~ \.php {
			fastcgi_param SCRIPT_FILENAME /var/www/html/opvvvweb/www/index.php;
			include fastcgi_params;
			fastcgi_pass unix:/var/run/php/php-fpm.sock;
			fastcgi_index index.php;
		}
	}

	location /css {
		alias /var/www/html/opvvvweb/www/css/;
	}
	location /js {
		alias /var/www/html/opvvvweb/www/js/;
	}
	location /clb.png {
		alias /var/www/html/opvvvweb/www/clb.png;
	}

	# AWSTATS
	location /awstats-icon {
		allow x.x.x.x/24;
		deny all;
		alias /usr/share/awstats/icon/;
		access_log off;
	}

	location /awstats {
		allow x.x.x.x/24;
		deny all;
		rewrite ^/awstats$ $scheme://$server_name/awstats/?config=vufind;
		proxy_pass http://127.0.0.1:42;
		access_log off;
	}

	# VUFIND
	location /themes/ {
		alias /usr/local/vufind/themes/;
	}

	location /cache/ {
		alias /usr/local/vufind/local/cache/;
	}

	location ~ api {
		try_files $uri $uri/ /index.php$is_args$args;
		allow x.x.x.x/24;
		deny all;
        }

	location / {
		root /usr/local/vufind/public/;
		try_files $uri $uri/ /index.php$is_args$args;
		index index.php;
	}

	location ~\.php$ {
		#fastcgi_param VUFIND_ENV development;
		fastcgi_param SCRIPT_FILENAME /usr/local/vufind/public/index.php;
		fastcgi_param VUFIND_LOCAL_DIR /usr/local/vufind/local/;
		fastcgi_param VUFIND_HOME /usr/local/vufind/;
		fastcgi_param VUFIND_LOCAL_MODULES CLB;
		include fastcgi_params;

		fastcgi_pass unix:/var/run/php/php-fpm.sock;
		fastcgi_index index.php;
	}
}

mysql_secure_installation

[enter] n Y xxx Y Y Y Y

wget https://github.com/vufind-org/vufind/releases/download/v8.0.3/vufind-8.0.3.tar.gz

tar xzf vufind-8.0.3.tar.gz

mv vufind-8.0.3 /usr/local/vufind

cd /usr/local/vufind

php install.php

chown -R www-data:www-data /usr/local/vufind/local/cache
chown -R www-data:www-data /usr/local/vufind/local/config

mkdir /usr/local/vufind/local/cache/cli

/etc/profile.d/vufind.sh:
export JAVA_HOME="/usr/lib/jvm/java-11-openjdk-amd64"
export VUFIND_HOME="/usr/local/vufind"
export VUFIND_LOCAL_DIR="$VUFIND_HOME/local"
export VUFIND_LOCAL_MODULES="CLB"

source /etc/profile.d/vufind.sh

adduser solr --disabled-password

chown -R solr:solr $VUFIND_HOME/solr

/etc/system.d/system/solr.service:
[Unit]
After=network.target nginx.service mariadb.service

[Service]
Type=forking
ExecStart=/bin/sh -l -c '/usr/local/vufind/solr.sh start' -x
PIDFile=/usr/local/vufind/solr/vendor/bin/solr-8983.pid
User=solr
ExecStop=/bin/sh -l -c "/usr/local/vufind/solr.sh stop" -x
SuccessExitStatus=0
LimitNOFILE=65000
LimitNPROC=65000

[Install]
WantedBy=multi-user.target

systemctl enable solr
systemctl start solr

http://xxx/Install/Home

xxx / xxx

NoILS

http://xxx/Install/done

/usr/local/vufind/local/config/vufind/NoILS.ini:
mode = ils-none

touch /var/log/vufind.log
chown www-data:www-data /var/log/vufind.log
</pre>
TUNE
<pre>
cp vufind-update.timer vufind-update.service /etc/systemd/system/
systemctl enable vufind-update.timer
systemctl start vufind-update.timer

/etc/crontab:
*/15 *  * * *   root    /usr/bin/awstats -config=xxx -update >> /var/log/awstats.log 2>&1 &
00 5	* * *	root	/root/bin/vufind-monitor.py > /dev/null 2>&1 &
30 6	* * *	root	find /tmp/vufind_sessions/&ast; -mtime +5 -exec rm {} \; > /dev/null &

touch /var/log/vufind.log
chown www-data:www-data /var/log/vufind.log
</pre>
MODULE
<pre>
php install.php
php public/index.php generate/extendclass VuFind\\RecordDriver\\SolrMarc CLB
</pre>
THEME
<pre>
php public/index.php generate/theme CLB
</pre>
CSS
<pre>
cd /usr/local/vufind/themes/CLB/less/
lessc --verbose bootprint.less compiled.css
mv compiled.css ../css/
</pre>
LANGUAGES
<pre>
php public/index.php language/normalize local/languages/cs.ini
rmdir -r local/cache/languages
</pre>
REINDEX
<pre>
systemctl stop solr
rm -rf $VUFIND_HOME/solr/vufind/biblio/index $VUFIND_HOME/solr/vufind/spell*
./vufind-manual-update.sh
</pre>
ALPHA
<pre>
rm -r /usr/local/vufind/solr/vufind/alphabetical_browse/
./index-alphabetic-browse.sh
systemctl restart solr
</pre>
STATIC ROUTE
<pre>
php public/index.php generate staticroute SAM CLB
(clear local cache)
</pre>
REBUILD SPELLCHECK
<pre>
Optimize index:
http://localhost:8983/solr/biblio/update?optimize=true

Rebuild "default" index:
http://localhost:8983/solr/biblio/select?q=*:*&spellcheck=true&spellcheck.build=true

Rebuild "basicSpell" index:
http://localhost:8983/solr/biblio/select?q=*:*&spellcheck.dictionary=basicSpell&spellcheck=true&spellcheck.build=true
</pre>

SOURCE

https://github.com/CzechLitBib/UCL

