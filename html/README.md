DESCRIPTION

PHP7 frontend.

INSTALL
<pre>
apt-get install nginx php7.3 php7.3-fpm php7.3-cli php7-gd php7-ldap php7-json php7.3-sqlite3

mkdir -p /var/www/html/.well-known/acme-challenge
chown -R www-data:www-data /var/www/html/.well-known
</pre>
PHP
<pre>
/etc/php/7.3/fpm/php.ini:
upload_max_filesize = 5M
systemctl php7.3-fpm restart
</pre>
NGINX
<pre>
server {
	listen 443 ssl;
	listen [::]:443 ssl;

	server_name xxx;

	root /var/www/html;

	index index.php;

	add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
	add_header X-Frame-Options "DENY";
	add_header X-Robots-Tag "noindex, nofollow, nosnippet, noarchive";

	ssl_certificate /etc/letsencrypt/live/xxx/fullchain.pem;
	ssl_certificate_key /etc/letsencrypt/live/xxx/privkey.pem;
	include /etc/letsencrypt/options-ssl-nginx.conf;

	error_page 497 https://xxx;

	client_max_body_size 5M;

	# Vufind API
	location ~ ^/api {
		allow xxx/24;
		allow 127.0.0.1;
		deny all;
		proxy_pass http://127.0.0.1:5000;
	}
	location ~ ^/static {
		allow xxx/24;
		allow 127.0.0.1;
		deny all;
		root /usr/local/bin/api/;
	}

	# Solr
	#location ~ ^/solr {
	#	allow xxx/24;
	#	allow 127.0.0.1;
	#	deny all;
	#	proxy_pass http://127.0.0.1:8983;
	#}

	# PHP - local
	location ~ ^/(?!form|cardio) {
		allow xxx/24;
		allow 127.0.0.1;
		deny all;

		location ~ \.php {
			fastcgi_split_path_info ^(.+\.php)(/.+)$;
			fastcgi_pass	unix:/var/run/php/php7.3-fpm.sock;
			fastcgi_index	index.php;
			fastcgi_param	SCRIPT_FILENAME $document_root$fastcgi_script_name;
			include		fastcgi_params;
		}
	}

	# PHP - form data + db
	location ~ ^/form/(data|db) {
		allow xxx/24;
		allow 127.0.0.1;
		deny all;
	}

	# PHP - fallback
	location ~ \.php {
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass	unix:/var/run/php/php7.3-fpm.sock;
		fastcgi_index	index.php;
		fastcgi_param	SCRIPT_FILENAME $document_root$fastcgi_script_name;
		include		fastcgi_params;
	}

}
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

