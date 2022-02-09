<?php

return array (
	'vufind' => 
	array (
		'plugin_managers' => 
		array (
			'recordtab' =>
			array (
				'abstract_factories' => array('VuFind\\RecordTab\\PluginFactory'),
				'invokables' => array(
					'techdata' => 'CLB\\RecordTab\\TechData',
				),
			),
			'recorddriver' => 
			array (
				'factories' => 
				array (
					'CLB\\RecordDriver\\SolrMarc' => 'VuFind\\RecordDriver\\SolrDefaultFactory',
				),
				'aliases' => 
				array (
					'VuFind\\RecordDriver\\SolrMarc' => 'CLB\\RecordDriver\\SolrMarc',
				),
				'delegators' => 
				array (
					'CLB\\RecordDriver\\SolrMarc' => 
					array (
						0 => 'VuFind\\RecordDriver\\IlsAwareDelegatorFactory',
          				),
        			),
      			),
    		),
	),
);
