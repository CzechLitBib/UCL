DESCRIPTION

Vufind server howto.

TODO
<pre>
bbg,rej - author-classification.ini translation.
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

(php-dev)

# JAVA

openjdk-11-jre-headless

# CERTBOT

certbot python3-certbot-nginx

# INSTALL

/etc/nginx/sites-enabled/default:

server {
	listen 80;

	server_name xxx;

	location /themes/ {
		alias /usr/local/vufind/themes/;
	}

	location /cache/ {
		alias /usr/local/vufind/local/cache/;
	}

	location / {
		root /usr/local/vufind/public/;
		try_files $uri $uri/ /index.php$is_args$args;
		index index.php;
	}

	location ~\.php$ {
		#fastcgi_param VUFIND_ENV development;
		#fastcgi_param APPLICATION_ENV development;
		fastcgi_param SCRIPT_FILENAME /usr/local/vufind/public/index.php;
		fastcgi_param VUFIND_LOCAL_DIR /usr/local/vufind/local/;
		fastcgi_param VUFIND_HOME /usr/local/vufind/;
		fastcgi_param VUFIND_LOCAL_MODULES SolrMarcUCL;
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
export VUFIND_LOCAL_MODULES="SolrMarcUCL"

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
</pre>
TUNE
<pre>
/etc/crontab:
'''
15 *	* * *	root	/root/oai-update.sh >> /var/log/oai-update.log 2>&1 &
00 5	* * *	root	/root/vufind_monitor.py > /dev/null 2>&1 &
30 6	* * *	root	find /tmp/vufind_sessions/&ast; -mtime +5 -exec rm {} \; > /dev/null &

/usr/local/vufind/public/robots.txt:
User-agent: *
Disallow: /

touch /var/log/vufind.log
chown www-data:www-data /var/log/vufind.log
</pre>
MODULE
<pre>
export VUFIND_LOCAL_MODULES=SolrMarcUCL

php public/index.php generate extendclass VuFind\\RecordDriver\\SolrMarc SolrMarcUCL
</pre>
THEME
<pre>
php public/index.php generate theme ThemeUCL
</pre>
LANGUAGES
<pre>
php public/index.php language normalize cs.ini

rmdir local/cache/languages
</pre>
