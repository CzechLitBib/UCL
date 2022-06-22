
TODO
<pre>
 PDF mod.
 DOCX mod init.
</pre>
INSTALL
<pre>
apt-get install uwsgi uwsgi-plugin-python3 python-flask python3-reportgen

svglib-1.3.0:

apt-get install python3-lxml python3-cssselect2 python3-tinycss2 python3-webencodings

python-docx-0.8.11

mkdir /usr/local/bin/export
cp -p vufind-export /usr/local/bin/export
cp -p clb.svg /usr/local/bin/export
chown -Rsolr:solr /usr/local/bin/export
cp vufind-export.service /etc/systemd/system/
systemctl enable vufind-export
systemctl start vufind-export

/etc/nginx/sites-enabled/default:

	# EXPORT
	location /export {
		include uwsgi_params;
		uwsgi_pass 127.0.0.1:5001;
		#proxy_pass http://127.0.0.1:5001;
	}

</pre>
VUFIND
<pre>
php public/index.php generate staticroute export CLB

/usr/local/vufind/themes/CLB/templates/search/bulk-action-buttons.phtml
</pre>
