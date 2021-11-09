
DESCRIPTION

Vufind REST API.

INSTALL
<pre>
apt-get install python3-flask python3-flask-restful

cp api /usr/locail/bin
ln -s /usr/local/bin/api/vufind-update /usr/local/bin/vufind-update
cp /usr/local/bin/api/vufind-api.service /etc/systemd/system/

systemctl enable vufind-api.service
</pre>
SOURCE

https://github.com/KyomaHooin/UCL

