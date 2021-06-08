#!/bin/bash
#
# CLI Koha export
#
export PERL5LIB='/usr/share/koha/lib'
export KOHA_CONF='/etc/koha/sites/koha/koha-conf.xml'

perl /usr/share/koha/bin/export_records.pl --record-type='auths' --filename='koha_clo.xml'

