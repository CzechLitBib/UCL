#!/bin/bash
#
# OAI-PMH 2.0 MARCXML Yearly statistic.
#

#oai-marc --set UCLA --from '2020-01-16 00:00:00' --until '2020-01-17 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-16.html
#sleep 10
#oai-marc --set UCLA --from '2020-01-17 00:00:00' --until '2020-01-18 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-17.html
#sleep 10
oai-marc --set UCLA --from '2020-01-18 00:00:00' --until '2020-01-19 00:00:00' --check
cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-18.html
sleep 10
#oai-marc --set UCLA --from '2020-01-19 00:00:00' --until '2020-01-20 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-19.html
#sleep 10
#oai-marc --set UCLA --from '2020-01-20 00:00:00' --until '2020-01-21 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-20.html
#sleep 10
#oai-marc --set UCLA --from '2020-01-21 00:00:00' --until '2020-01-22 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-21.html
#sleep 10
#oai-marc --set UCLA --from '2020-01-22 00:00:00' --until '2020-01-23 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-22.html
#sleep 10
#oai-marc --set UCLA --from '2020-01-23 00:00:00' --until '2020-01-24 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-23.html
#sleep 10
#oai-marc --set UCLA --from '2020-01-24 00:00:00' --until '2020-01-25 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-24.html
#sleep 10
oai-marc --set UCLA --from '2020-01-25 00:00:00' --until '2020-01-26 00:00:00' --check
cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-25.html
#sleep 10
#oai-marc --set UCLA --from '2020-01-26 00:00:00' --until '2020-01-26 00:00:00' --check
#cat oai-marc.html > /var/www/html/archiv/2020/01/2020-01-26.html

exit 0

