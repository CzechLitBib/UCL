<?php
 
 namespace CLB\View\Helper\Root;
 
 use VuFind\View\Helper\Root\RecordDataFormatter\SpecBuilder;
 
 class RecordDataFormatterFactory extends \VuFind\View\Helper\Root\RecordDataFormatterFactory
 {
     // Add Bulk Tab item
     public function getDefaultDescriptionSpecs()
     {
         $spec = new SpecBuilder(parent::getDefaultDescriptionSpecs());
         $spec->setLine('Conspectus', 'getMoreInfo');
         $spec->setLine('MDT', 'getConspectGroup');
         return $spec->getArray();
     }

     // Translate language index
     public function getDefaultCollectionInfoSpecs()
     {
         $spec = new SpecBuilder(parent::getDefaultCollectionInfoSpecs());
         $spec->setLine(
            'Language', 'getLanguages', null,
            ['itemPrefix' => '<span property="availableLanguage" typeof="Language">'
                           . '<span property="name">',
             'itemSuffix' => '</span></span>', 'translate' => true]
         );
         return $spec->getArray();
     }

     public function getDefaultCollectionRecordSpecs()
     {
         $spec = new SpecBuilder(parent::getDefaultCollectionRecordSpecs());
         $spec->setLine(
            'Language', 'getLanguages', null,
            ['itemPrefix' => '<span property="availableLanguage" typeof="Language">'
                           . '<span property="name">',
             'itemSuffix' => '</span></span>', 'translate' => true]
         );
         return $spec->getArray();
     }

     public function getDefaultCoreSpecs()
     {
         $spec = new SpecBuilder(parent::getDefaultCoreSpecs());
         $spec->setLine(
            'Language', 'getLanguages', null,
            ['itemPrefix' => '<span property="availableLanguage" typeof="Language">'
                           . '<span property="name">',
             'itemSuffix' => '</span></span>', 'translate' => true]
         );
         return $spec->getArray();
     }

 }

