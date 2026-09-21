DESCRIPTION

PHP7 frontend.

INSTALL
<pre>
apt-get install nginx php7.3 php7.3-fpm php7.3-cli php7-gd php7-ldap php7-json php7.3-sqlite3

mkdir -p /var/www/html/.well-known/acme-challenge
chown -R www-data:www-data /var/www/html/.well-known
</pre>
SASS
<pre>
wget https://github.com/sass/dart-sass/releases/download/1.50.1/dart-sass-1.50.1-linux-x64.tar.gz
wget https://github.com/twbs/bootstrap/archive/v5.1.3.zip

/custom/bootstrap/*.scss
/custom/scss/custom.scss

sass --no-source-map --style=compressed custom.scss custom.css
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

	error_page 403 /4xx/403.html;
	error_page 404 /4xx/404.html;

	client_max_body_size 5M;

	# 4xx
	location ~ /4xx {
		allow all;
	}

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

	# Aleph URL redirect
	location ~ "^/[0-9]{9}" {
		allow xxx/24;
		allow 127.0.0.1;
		deny all;
		rewrite ^/(.*)$ https://aleph.lib.cas.cz/F/?func=direct&doc_number=$1&local_base=AV&format=001 permanent;
	}
	location ~ "^/aleph/[0-9]{9}" {
		allow xxx./24;
		allow 127.0.0.1;
		deny all;
		rewrite ^/aleph/(.*)$ https://aleph.lib.cas.cz/F/?func=direct&doc_number=$1&local_base=AV&format=001 permanent;
	}
	location ~ "^/vufind/[0-9]{9}" {
		allow xxx/24;
		allow 127.0.0.1;
		deny all;
		rewrite ^/vufind/(.*)$ https://vufind.ucl.cas.cz/Record/$1#details permanent;
	}

	# PHP - local
	location / {
		allow xxx/24;
		allow xxx/25;
		allow 127.0.0.1;
		deny all;

		location ~ \.php {
			fastcgi_split_path_info ^(.+\.php)(/.+)$;
			fastcgi_pass	unix:/var/run/php/php7.3-fpm.sock;
			fastcgi_index	index.php;
			fastcgi_param	SCRIPT_FILENAME $document_root$fastcgi_script_name;
			fastcgi_intercept_errors on;
			include		fastcgi_params;
		}
	}

	# PHP - fallback
	location ~ \.php {
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass	unix:/var/run/php/php7.3-fpm.sock;
		fastcgi_index	index.php;
		fastcgi_param	SCRIPT_FILENAME $document_root$fastcgi_script_name;
		fastcgi_intercept_errors on;
		include		fastcgi_params;
	}

}

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

	error_page 403 /4xx/403.html;
	error_page 404 /4xx/404.html;

	client_max_body_size 5M;

	# 4xx
	location ~ /4xx {
		allow all;
	}

	location ~ ^/(?!konsorcium_form) {
		return 301 /konsorcium_form/;
	}

	# PHP - fallback
	location ~ \.php {
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass	unix:/var/run/php/php7.3-fpm.sock;
		fastcgi_index	index.php;
		fastcgi_param	SCRIPT_FILENAME $document_root$fastcgi_script_name;
		fastcgi_intercept_errors on;
		include		fastcgi_params;
	}
}

</pre>
SOURCE

https://github.com/CzechLitBib/UCL

