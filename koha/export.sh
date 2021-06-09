#!/bin/bash
#
# CLI Koha export
#
# Type: auths | bibs
#
export PERL5LIB='/usr/share/koha/lib'
export KOHA_CONF='/etc/koha/sites/koha/koha-conf.xml'

perl /usr/share/koha/bin/export_records.pl --record-type='auths' --format='xml' --filename='koha.xml'

