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
		$helper->setDefaults('techdata', [$this, 'getDefaultTechDataSpecs']);

		// Authority
		$helper->setDefaults('auth-core', [$this, 'getDefaultAuthCoreSpecs']);
		$helper->setDefaults('search-auth-info', [$this, 'getDefaultSearchAuthInfoSpecs']);

		return $helper;
	}

	protected function CLB_getAuthorFunction()
	{
		return function ($data, $options) {
			$labels = ['primary' => ['Main Author', 'Main Authors']];
			$schemaLabels = ['primary' => 'author'];
			$order = ['primary' => 1];
			$final = [];

			foreach ($data as $type => $values) {
				$final[] = [
					'label' => $labels[$type][count($values) == 1 ? 0 : 1],
					'values' => [$type => $values],
					'options' => [
						'pos' => $options['pos'] + $order[$type],
						'renderType' => 'RecordDriverTemplate',
						'template' => 'data-result-authors.phtml',
						'context' => [
							'type' => $type,
							'schemaLabel' => $schemaLabels[$type],
							'requiredDataFields' => [
								['name' => 'role', 'prefix' => 'CreatorRoles::']
							],
						],
					],
				];
			}
			return $final;
		};
	}

	// CORE
	public function getDefaultCoreSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setLine('Statement of Responsibility','CLB_getResponsibility');
		$spec->setMultiLine('Authors', 'getDeduplicatedAuthors', $this->getAuthorFunction());
		$spec->setLine('Cypher/signature','CLB_getCypher');
		$spec->setTemplateLine('Original Name','CLB_getOriginal', 'data-original.phtml');
		$spec->setLine('Format', 'getFormats', 'RecordHelper',['helperMethod' => 'getFormatList']);
		$spec->setLine(
			'Language', 'getLanguages', null,
			['itemPrefix' => '<span property="availableLanguage" typeof="Language">'
			. '<span property="name">',
			'itemSuffix' => '</span></span>', 'translate' => true]
		);
		$spec->setLine('Country', 'CLB_getCountry', null,
			['itemPrefix' => '<span property="availableCountry" typeof="Country">'
			. '<span property="name">',
			'itemSuffix' => '</span></span>', 'translate' => true]
		);
		$spec->setTemplateLine('Series', 'getSeries', 'data-series.phtml');
		$spec->setTemplateLine('Info','CLB_getInfo', 'data-info.phtml');
		$spec->setTemplateLine('Published', 'getPublicationDetails', 'data-publicationDetails.phtml');
		$spec->setTemplateLine('Form/Genre','CLB_getGenre', 'data-genre.phtml');
		$spec->setTemplateLine('Related work', 'CLB_getRelated', 'data-related.phtml');
		$spec->setTemplateLine('Subjects', 'getAllSubjectHeadings', 'data-allSubjectHeadings.phtml');
		$spec->setTemplateLine('Online Access', true, 'data-onlineAccess.phtml');
		$spec->setTemplateLine('Tags', true, 'data-tags.phtml');
		$spec->setTemplateLine('Actual Excerption', 'CLB_getActualExcerption', 'data-excerption.phtml');
		$spec->setTemplateLine('Finished Excerption', 'CLB_getFinishedExcerption', 'data-excerption.phtml');
		$spec->setLine('Citation','CLB_getCitation');
		$spec->setTemplateLine('Relations', 'CLB_getRelations', 'data-relations.phtml');
		return $spec->getArray();
	}

	// AUTH CORE
	public function getDefaultAuthCoreSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setTemplateLine('Use for', 'getUseFor', 'data-usefor.phtml');
		$spec->setTemplateLine('Occupation', 'CLB_getOccupation', 'data-occupation.phtml');
		$spec->setTemplateLine('Field of activity', 'CLB_getActivity', 'data-activity.phtml');
		$spec->setTemplateLine('Associated Group', 'CLB_getAssocGroup','data-associated-group.phtml');
		$spec->setTemplateLine('Honorific', 'CLB_getHonorific','data-honorific.phtml');
		$spec->setTemplateLine('Gender', 'CLB_getGender','data-gender.phtml');
		$spec->setTemplateLine('Translation from', 'CLB_getLanguageFrom','data-translated-from.phtml');
		$spec->setTemplateLine('Translation to', 'CLB_getLanguageTo','data-translated-to.phtml');
		$spec->setTemplateLine('Other associated place', 'CLB_getRelatedPlace','data-related-place.phtml');
		$spec->setTemplateLine('Country', 'CLB_getCountry','data-country.phtml');
		$spec->setLine('Note', 'CLB_getNote');
		return $spec->getArray();
	}

	// RESULT-LIST
	public function getDefaultSearchInfoSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setMultiLine('Authors', 'CLB_getDeduplicatedPrimaryAuthors', $this->CLB_getAuthorFunction());
		$spec->setTemplateLine('Published', 'getPublicationDetails', 'data-publication.phtml');
		$spec->setLine('Excerption Period', 'CLB_getExcerptionPeriod');
		$spec->setTemplateLine('Info','CLB_getInfo', 'data-info.phtml');
		$spec->setLine('Annotation','CLB_getAnnotationShort');
		$spec->setTemplateLine('Online Access', true, 'data-online.phtml');
		return $spec->getArray();
	}

	// AUTH-RESULT-LIST
	public function getDefaultSearchAuthInfoSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setTemplateLine('Use for', 'getUseFor','data-usefor.phtml');
		$spec->setLine('Birth', 'CLB_getBirth');
		$spec->setLine('Death', 'CLB_getDeath');
		$spec->setLine('Bio', 'CLB_getBio');
		return $spec->getArray();
	}

	// BULK DESCRIPTION
	public function getDefaultDescriptionSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setLine('Conspectus', 'CLB_getConspectGroup');
		$spec->setLine('MDT', 'CLB_getMDT');
		$spec->setLine('Item Description', 'getGeneralNotes');
		$spec->setLine('Physical Description', 'getPhysicalDescriptions');
		$spec->setLine('Publication Frequency', 'getPublicationFrequency');
		$spec->setLine('ISBN', 'getISBNs', null, ['itemPrefix' => '<span property="isbn">', 'itemSuffix' => '</span>']);
		$spec->setLine('ISSN', 'getISSNs', null, ['itemPrefix' => '<span property="issn">', 'itemSuffix' => '</span>']);
		return $spec->getArray();
	}

	// BULK TECH DATA
	public function getDefaultTechDataSpecs()
	{
		$spec = new SpecBuilder();
		$spec->setLine('System Number', 'getUniqueID');
		$spec->setLine('Record Creation', 'CLB_getCreationDate');
		$spec->setLine('Record Edit', 'CLB_getEditDate');
		$spec->setLine('Excerptor', 'CLB_getExcerptor');
		return $spec->getArray();
	}

	// FIX LAGUAGE
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

