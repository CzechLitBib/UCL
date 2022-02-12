DESCRIPTION

Vufind server howto.

TODO
<pre>
-article_resource_ dup
-Title: clean too much..(/ only)
-mod hardcoded paths (module + theme)
-In: 773 jenom s "g"
-In: (book)/chapter(LDR8 an) => 773 == 787 case.
-related_doc_txt_mv CLB_getRelated() ? "Odkozovane dilo":

 a) z pole 630 (významná menšina) pro zobrazení stačí 630a. 630l. 630p. 630s
 b) z pole 787  pro zobrazení prosím pořadí:
   787a. -- 787t. -- 787n. -- 787b. -- 787d. -- 787k. -- 787h. -- 787x. -- 787z. [787-4]
   (za polem je vždy tečka, možná už se tam ale bude dostávat přímo ze záznamu;
   prosím, raději ověřte, aby nevznikaly dvě tečky po sobě; oddělující znak je spojovník = krátká pomlčka)
 c)pro fasetu prosím stávající 787a 787t (t je vždy, a nemusí být pokaždé) nebo 630alps
 d)pro hyperlink v detailu z. prosím to stejné (787a+t, nebo 630alps)

-Result list - "main author or nothing"
-Core: Anotace pod titulkem cela(!) z tabulky pric.
-en.ini + Advanced search(En): Czech facet ??
-file chown/perm check
-Diacritic sort order. MZK
-In: highlight
-Core: flex: 1
-Advanced search: facet HTML placeholder
-b) zobrazení jednotlivého záznamu v rešerši - prosím upravit zalomování
 a návěští tak, jak má stávající instalace (kvůli úspoře místa, tj. nedosazovat tabulátorem,
 stačí jedna mezera. návěští zarovnat vlevo nahoru)
-b1) prosím též zmenšit odsazení mezi názvy polí a hodnotami u zobrazení jednotlivého záznamu

-Nginx API ACL
-ENV_VAR = 'devel'
-SSL
-Seznam tipu poli od vyrobce. core/result @.
-Dismax @
-SAM Grafy
-bbg,rej - author-classification.ini translation.
-Cover ? 002712500

-spell check
-SAM Grafy
-Nahled Retrobi:

Pokud by šlo, připravit následující 2 věci, prosím, upravte, pokud by to mělo být na delší úpravu, nechte být (ve stávající verzi to není)
a) Prosím u záznamu zobrazit náhled lístku nad tabulkový rozpis v proporcích dle vzoru zde: https://atelier-tippman.cz/UCL/webCLB/v6/VuFind_detail_RETROBI.html  (hned pod název);
Link na lístek je v poli 856
b) Pakliže není vyplněna anotace (520), prosím hned pod obrázek dát s návěštím „OCR přepis lístku:“ obsah pole 989a
Další úpravy pak není třeba řešit, uděláme pak jako jeden větší balík

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

# CSS

node-less

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
</pre>
TUNE
<pre>
/etc/crontab:
'''
15 *	* * *	root	/root/vufind-update.sh >> /var/log/vufind-update.log 2>&1 &
00 5	* * *	root	/root/vufind-monitor.py > /dev/null 2>&1 &
30 6	* * *	root	find /tmp/vufind_sessions/&ast; -mtime +5 -exec rm {} \; > /dev/null &

/usr/local/vufind/public/robots.txt:
User-agent: *
Disallow: /

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
./index-alphabetic-browse.sh
</pre>

