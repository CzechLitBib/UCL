<?php

/* Domain name of the Solr server */
define('SOLR_SERVER_HOSTNAME', 'xxx');

/* Whether or not to run in secure mode */
define('SOLR_SECURE', false);

/* HTTP Port to connection */
define('SOLR_SERVER_PORT', ((SOLR_SECURE) ? 8443 : 8983));

/* HTTP connection timeout */
/* This is maximum time in seconds allowed for the http data transfer operation. Default value is 30 seconds */
define('SOLR_SERVER_TIMEOUT', 10);

/* Solr Core path */
define('SOLR_PATH', 'solr/xxx');

/* Solr Query limit */
define('SOLR_QUERY_LIMIT', 10000);

?>

