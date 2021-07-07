NGINX
<pre>
server {
	listen 80;
	listen [::]:80;

	root /var/www/html;

	server_name vyvoj.ucl.cas.cz;

	location /.well-known/acme-challenge/ {
		try_files $uri $uri/ =404;
	}

	location / {
		return 301 https://vyvoj.ucl.cas.cz;
	}
}

server {
	#listen 443 ssl;
	#listen [::]:443 ssl;
	listen 4433 ssl;
	listen [::]:4433 ssl;

	server_name vyvoj.ucl.cas.cz;

	root /var/www/html;

	index index.php;

	ssl_certificate /etc/letsencrypt/live/vyvoj.ucl.cas.cz/fullchain.pem;
	ssl_certificate_key /etc/letsencrypt/live/vyvoj.ucl.cas.cz/privkey.pem;
	include /etc/letsencrypt/options-ssl-nginx.conf;

	#error_page 497 https://vyvoj.ucl.cas.cz;
	error_page 497 https://vyvoj.ucl.cas.cz:4433;

	location ~ \.php {
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass	unix:/var/run/php/php7.3-fpm.sock;
		fastcgi_index	index.php;
		fastcgi_param	SCRIPT_FILENAME $document_root$fastcgi_script_name;
		include		fastcgi_params;
	}

	location /clanky/data {
		deny all;
		return 403;
	}

	location /api {
		proxy_pass http://127.0.0.1:5000;
	}
}
</pre>
SOURCE

https://github.com/KyomaHooin/UCL