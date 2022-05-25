<?php

return array (
  'vufind' => 
  array (
    'plugin_managers' => 
    array (
      'recordtab' => 
      array (
        'abstract_factories' => 
        array (
          0 => 'VuFind\\RecordTab\\PluginFactory',
        ),
        'invokables' => 
        array (
          'techdata' => 'CLB\\RecordTab\\TechData',
        ),
      ),
      'recorddriver' => 
      array (
        'factories' => 
        array (
          'CLB\\RecordDriver\\SolrMarc' => 'VuFind\\RecordDriver\\SolrDefaultFactory',
          'CLB\\RecordDriver\\SolrAuthMarc' => 'VuFind\\RecordDriver\\SolrDefaultWithoutSearchServiceFactory',
        ),
        'aliases' => 
        array (
          'VuFind\\RecordDriver\\SolrMarc' => 'CLB\\RecordDriver\\SolrMarc',
          'VuFind\\RecordDriver\\SolrAuthMarc' => 'CLB\\RecordDriver\\SolrAuthMarc',
        ),
        'delegators' => 
        array (
          'CLB\\RecordDriver\\SolrMarc' => 
          array (
            0 => 'VuFind\\RecordDriver\\IlsAwareDelegatorFactory',
          ),
        ),
      ),
      'auth' => 
      array (
        'factories' => 
        array (
          'CLB\\Auth\\MultiAuth' => 'VuFind\\Auth\\MultiAuthFactory',
          'CLB\\Auth\\ChoiceAuth' => 'VuFind\\Auth\\ChoiceAuthFactory',
        ),
        'aliases' => 
        array (
          'VuFind\\Auth\\MultiAuth' => 'CLB\\Auth\\MultiAuth',
          'VuFind\\Auth\\ChoiceAuth' => 'CLB\\Auth\\ChoiceAuth',
        ),
      ),
    ),
  ),
  'router' => 
  array (
    'routes' => 
    array (
      'sam' => 
      array (
        'type' => 'Laminas\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/SAM',
          'defaults' => 
          array (
            'controller' => 'SAM',
            'action' => NULL,
          ),
        ),
      ),
      'export' => 
      array (
        'type' => 'Laminas\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/export',
          'defaults' => 
          array (
            'controller' => 'export',
            'action' => NULL,
          ),
        ),
      ),
    ),
  ),
);
