INSTALL
<pre>
# BASE

git net-tools tcpdump mc

# SSL

apt-get install apache2 certbot

certbot python3-certbot-apache

a2enmod rewrite ssl headers

mkdir -p /var/www/html/.well-known/acme-challenge
chown -R www-data:www-data /var /www/html/.well-known

certbot certonly -w /var/www/html -d xxx

[renewalparams]

authenticator = webroot
webroot_path = /var/www/html

# KOHA

apt-get install gnupg1

wget https://debian.koha-community.org/koha/gpg.asc

apt-key add gpg.asc

apt-get install mariadb-server postfix

/etc/apt/sources.list.d/koha.list:

deb http://debian.koha-community.org/koha stable main

apt-get install koha-common

a2enmod proxy_http cgi

/etc/koha/koha-sites.conf:

DOMAIN="xxx"
INTRAPORT="443"
INTRAPREFIX=""
INTRASUFFIX=""

ZEBRA_LANGUAGE='cs'

koha-create --create-db instance

koha-plack --enable instance
koha-plack --start instance

koha-translate --install cs-CZ

vi /etc/apache2/sites-available/clo.conf

https://xxx

# KOHA REINSTALL

koha-remove -p instance

# BUG

Failed to enable unit: Unit /run/systemd/generator.late/koha-common.service is transient or generated.

# KOHA LDAP

<useldapserver>1</useldapserver>
<ldapserver>
        <hostname>ldap://xxx</hostname>
        <base>xxx</base>
        <auth_by_bind>1</auth_by_bind>
        <anonymous_bind>0</anonymous_bind>
        <principal_name>xxx</rincipal_name>
        <replicate>0</replicate>
        <update>0</update>
        <update_password>0</update_password>
        <mapping>
                <userid is="uid"></userid>
        </mapping>
</ldapserver>

# API

apt-get install python3-lxml python3-six

pyoai-2.5.0.tar.gz
pymarc-4.0.0.tar.gz

pyoai.patch
pymarc.patch

apt-get install python3-flask python3-restful

/usr/local/bin/api/

apt-get install libapache2-mod-wsgi-py3
</pre>
Z3950
<pre>
koha-z3950-responder --enable instance
</pre>
OAI
<pre>
koha-shell <instance>
/usr/share/koha/bin/migration_tools/build_sets_oai.pl -v

OAI.xslt  -> /usr/share/koha/opac/htdocs/opac-tmpl/xslt/OAI.xslt
PNP.xsl -> /usr/share/koha/intranet/htdocs/intranet-tmpl/prog/cs-CZ/xslt/PNP.xsl
</pre>
