TODO
<pre>
-Paragraph styling.
-Data loop.
</pre>

INSTALL
<pre>
apt-get install uwsgi python-flask python3-reportgen

svglib-1.3.0
python-docx-0.8.11
#pymarc-4.0.0 + field.py.patch

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

cp content/export.phtml /usr/local/vufind/themes/CLB/templates/content/
chown spravce:spravce /usr/local/vufind/themes/CLB/templates/content/export.phtml

/usr/local/vufind/themes/CLB/templates/search/bulk-action-buttons.phtml
</pre>
