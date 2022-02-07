<?php
 
namespace CLB\View\Helper\Root;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use VuFind\View\Helper\Root\RecordDataFormatter\SpecBuilder;
 
class RecordDataFormatterFactory extends \VuFind\View\Helper\Root\RecordDataFormatterFactory
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		if (!empty($options)) {
			throw new \Exception('Unexpected options sent to factory.');
		}
		$helper = new $requestedName();
		$helper->setDefaults('search-info', [$this, 'getDefaultSearchInfoSpecs']);
		$helper->setDefaults('collection-info', [$this, 'getDefaultCollectionInfoSpecs']);
		$helper->setDefaults('collection-record', [$this, 'getDefaultCollectionRecordSpecs']);
		$helper->setDefaults('core', [$this, 'getDefaultCoreSpecs']);
		$helper->setDefaults('description', [$this, 'getDefaultDescriptionSpecs']);
		return $helper;
	}

	// CORE
	public function getDefaultCoreSpecs()
	{
		//$spec = new SpecBuilder(parent::getDefaultCoreSpecs());
		$spec = new SpecBuilder();
		$spec->setLine('Statement of Responsibility','CLB_getResponsibility');
		$spec->setMultiLine('Authors', 'getDeduplicatedAuthors', $this->getAuthorFunction());
		$spec->setLine('Format', 'getFormats', 'RecordHelper',['helperMethod' => 'getFormatList']);
		$spec->setLine(
			'Language', 'getLanguages', null,
			['itemPrefix' => '<span property="availableLanguage" typeof="Language">'
			. '<span property="name">',
			'itemSuffix' => '</span></span>', 'translate' => true]
		);
		$spec->setTemplateLine('Published', 'getPublicationDetails', 'data-publicationDetails.phtml');
		$spec->setTemplateLine('Series', 'getSeries', 'data-series.phtml');
		$spec->setTemplateLine('In','CLB_getIn', 'data-in.phtml');
		$spec->setTemplateLine('Form/Genre','CLB_getGenre', 'link-genre.phtml');
		$spec->setTemplateLine('Related work', 'CLB_getRelated', 'data-related.phtml');
		$spec->setTemplateLine('Subjects', 'getAllSubjectHeadings', 'data-allSubjectHeadings.phtml');
		$spec->setTemplateLine('Online Access', true, 'data-onlineAccess.phtml');
		$spec->setTemplateLine('Tags', true, 'data-tags.phtml');
		$spec->setLine('Actual Excerption','CLB_getActualExcerption');
		$spec->setLine('Finished Excerption','CLB_getFinishedExcerption');
		$spec->setLine('Citation','CLB_getCitation');
		return $spec->getArray();
	}

	// RESULT-LIST
	public function getDefaultSearchInfoSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setMultiLine('Authors', 'getDeduplicatedAuthors', $this->getAuthorFunction());
		$spec->setTemplateLine('Published', 'getPublicationDetails', 'data-publicationDetails.phtml');
		$spec->setTemplateLine('In','CLB_getIn', 'data-in.phtml');
		$spec->setLine('Annotation','CLB_getAnnotation');
		$spec->setTemplateLine('Online Access', true, 'data-onlineAccess.phtml');
		return $spec->getArray();
	}

	// BULK DESCRIPTION
	public function getDefaultDescriptionSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setTemplateLine('Summary', true, 'data-summary.phtml');
		$spec->setLine('Conspectus', 'CLB_getMoreInfo');
		$spec->setLine('MDT', 'CLB_getConspectGroup');
		$spec->setLine('Item Description', 'getGeneralNotes');
		$spec->setLine('Physical Description', 'getPhysicalDescriptions');
		$spec->setLine('ISBN', 'getISBNs', null, ['itemPrefix' => '<span property="isbn">', 'itemSuffix' => '</span>']);
		$spec->setLine('ISSN', 'getISSNs', null, ['itemPrefix' => '<span property="issn">', 'itemSuffix' => '</span>']);
		$spec->setLine('Access', 'getAccessRestrictions');
		return $spec->getArray();
	}

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
}

