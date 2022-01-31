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
        
         $spec->setLine('Statement of Responsibility','getResponsibility');
         $spec->setLine(
            'Language', 'getLanguages', null,
            ['itemPrefix' => '<span property="availableLanguage" typeof="Language">'
                           . '<span property="name">',
             'itemSuffix' => '</span></span>', 'translate' => true]
         );
         $spec->setTemplateLine('In','getMisto', 'data-in.phtml');
         $spec->setTemplateLine('Form/Genre','getZanr', 'link-genre.phtml');
	 $spec->setLine('Citation','getCitation');
         $spec->setTemplateLine('Referred work','getOdkazovaneDilo', 'data-reffered.phtml');
         $spec->reorderKeys([
             'Statement of Responsibility',
             'Published in',
             'New Title',
             'Previous Title',
             'Authors',
             'Format',
             'Language',
             'In',
             'Form/Genre',
             'Referred work',
             //'Published',
             //'Edition',
             //'Series',
             'Subjects',
             //'child_records',
             'Online Access',
             //'Related items',
             'Tags',
             'Citation'
         ]);
         return $spec->getArray();
     }

 }

