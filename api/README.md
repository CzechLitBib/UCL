
DESCRIPTION

Vufind REST API.

INSTALL
<pre>
apt-get install python3-flask python3-flask-restful python3-rrdtool

cp -r api /usr/local/bin/
ln -s /usr/local/bin/api/vufind-update /usr/local/bin/vufind-update
cp /usr/local/bin/api/vufind-api.service /etc/systemd/system/

systemctl enable vufind-api.service

dpkg-reconfigure locales
cs_CZ.UTF-8
</pre>
SOURCE

https://github.com/CzechLitBib/UCL

