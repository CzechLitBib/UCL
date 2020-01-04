#!/bin/bash

./oai-marc.py --set UCLA --from '2019-01-01 00:00:00' --until '2019-01-31 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/jan.html
mv error.log /var/www/html/jan-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-02-01 00:00:00' --until '2019-02-28 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/feb.html
mv error.log /var/www/html/feb-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-03-01 00:00:00' --until '2019-03-31 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/mar.html
mv error.log /var/www/html/mar-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-04-01 00:00:00' --until '2019-04-30 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/apr.html
mv error.log /var/www/html/apr-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-05-01 00:00:00' --until '2019-05-31 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/may.html
mv error.log /var/www/html/may-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-06-01 00:00:00' --until '2019-06-30 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/jun.html
mv error.log /var/www/html/jun-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-07-01 00:00:00' --until '2019-07-31 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/jul.html
mv error.log /var/www/html/jul-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-08-01 00:00:00' --until '2019-08-31 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/aug.html
mv error.log /var/www/html/aug-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-09-01 00:00:00' --until '2019-09-30 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/sep.html
mv error.log /var/www/html/sep-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-10-01 00:00:00' --until '2019-10-31 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/oct.html
mv error.log /var/www/html/oct-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-11-01 00:00:00' --until '2019-11-30 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/nov.html
mv error.log /var/www/html/nov-err.log
sleep 300
./oai-marc.py --set UCLA --from '2019-12-01 00:00:00' --until '2019-12-31 23:59:59' --check >> error.log 2>&1
mv oai-marc.html /var/www/html/dec.html
mv error.log /var/www/html/dec-err.log
sleep 300

