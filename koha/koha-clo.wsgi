#!/usr/bin/python3
#
# KOHA CLO WSGI
#

import sys
sys.path.insert(0, '/usr/local/bin')

from api.koha_clo_api import app as application

