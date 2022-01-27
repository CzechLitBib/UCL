<?php
 
 namespace CLB\View\Helper\Root;
 
 use VuFind\View\Helper\Root\RecordDataFormatter\SpecBuilder;
 
 class RecordDataFormatterFactory extends \VuFind\View\Helper\Root\RecordDataFormatterFactory
 {
     public function getDefaultDescriptionSpecs()
     {
         $spec = new SpecBuilder(parent::getDefaultDescriptionSpecs());
         $spec->setLine('Conspectus', 'getMoreInfo');
         $spec->setLine('MDT', 'getConspectGroup');
         return $spec->getArray();
     }
 }

