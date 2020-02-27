#!/bin/bash
#
# OAI-PMH 2.0 MARCXML Yearly statistic.
#

/home/bruna/oai-7 --set UCLA --from '2019-01-01 00:00:00' --until '2019-02-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-02-01 00:00:00' --until '2019-03-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-03-01 00:00:00' --until '2019-04-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-04-01 00:00:00' --until '2019-05-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-05-01 00:00:00' --until '2019-06-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-06-01 00:00:00' --until '2019-07-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-07-01 00:00:00' --until '2019-08-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-08-01 00:00:00' --until '2019-09-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-09-01 00:00:00' --until '2019-10-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-10-01 00:00:00' --until '2019-11-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-11-01 00:00:00' --until '2019-12-01 00:00:00' --check >> 7.log
sleep 10
/home/bruna/oai-7 --set UCLA --from '2019-12-01 00:00:00' --until '2020-01-01 00:00:00' --check >> 7.log
sleep 10
#/home/bruna/oai-7 --set UCLA --from '2020-01-01 00:00:00' --until '2020-02-01 00:00:00' --check >> 7.log

exit 0

