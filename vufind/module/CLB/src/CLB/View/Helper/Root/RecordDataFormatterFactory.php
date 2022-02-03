<?php
 
namespace CLB\View\Helper\Root;
 
use VuFind\View\Helper\Root\RecordDataFormatter\SpecBuilder;
 
class RecordDataFormatterFactory extends \VuFind\View\Helper\Root\RecordDataFormatterFactory
{

	// result-list additional specs
	public function getDefaultSearchInfoSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setLine(
			'Language', 'getLanguages', null,
			['itemPrefix' => '<span property="availableLanguage" typeof="Language">'
			. '<span property="name">',
			'itemSuffix' => '</span></span>', 'translate' => true]
		);
		// $spec->setTemplateLine('In','CLB_getIn', 'data-in.phtml');
		// $spec->setLine('Annotation','CLB_getAnnotation');
		return $spec->getArray();
	}

	// Bulk Tab
	//public function getDefaultDescriptionSpecs()
	//{
	//	$spec = new SpecBuilder(parent::getDefaultDescriptionSpecs());
	//	$spec->setLine('Conspectus', 'CLB_getMoreInfo');
	//	$spec->setLine('MDT', 'CLB_getConspectGroup');
	//	return $spec->getArray();
	//}

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

	// Record detail
	public function getDefaultCoreSpecs()
	{
		$spec = new SpecBuilder(parent::getDefaultCoreSpecs());
		//$spec = new SpecBuilder();
		//$pec->setLine('Statement of Responsibility','CLB_getResponsibility');
		$spec->setLine(
			'Language', 'getLanguages', null,
			['itemPrefix' => '<span property="availableLanguage" typeof="Language">'
			. '<span property="name">',
			'itemSuffix' => '</span></span>', 'translate' => true]
		);
		//$spec->setTemplateLine('In','CLB_getIn', 'data-in.phtml');
		//$spec->setTemplateLine('Form/Genre','CLB_getGenre', 'link-genre.phtml');
		//$spec->setLine('Citation','CLB_getCitation');
		//$spec->setTemplateLine('Related work', 'CLB_getRelated', 'data-related.phtml');
		//$spec->reorderKeys([
			//'Statement of Responsibility',
			//'Published in',
			//'New Title',
			//'Previous Title',
			//'Authors',
			//'Format',
			//'Language',
			//'In',
			//'Form/Genre',
			//'Related work',
			//'Online Access',
			//'Tags',
			//'Citation'
		//]);
		return $spec->getArray();
	}
}

