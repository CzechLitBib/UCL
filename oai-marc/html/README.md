NGINX
<pre>
server {
	listen 80;
	listen [::]:80;

	root /var/www/html;

	server_name xxx;

	location /.well-known/acme-challenge/ {
		try_files $uri $uri/ =404;
	}

	location / {
		return 301 https://xxx;
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

	error_page 497 https://xxx;

	client_max_body_size 5M;

	# API
	location ~ ^/api {
		allow xxx/24;
		deny all;
		proxy_pass http://127.0.0.1:5000;
	}

	# PHP - local
	location ~ ^/(?!form|cardio) {
		allow xxx/24;
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

