<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * @copyright 2013,2014 bit3 UG
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @link      http://bit3.de
 * @package   bit3/contao-merger2
 * @license   LGPL-3.0+
 */


/**
 * Table tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('tl_module_merger2', 'onload');


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['Merger2'] = '{title_legend},name,headline,type;{config_legend},merger_mode,merger_data;{template_legend:hide},merger_template,merger_container;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['merger_mode'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_mode'],
	'inputType' => 'select',
	'options'   => &$GLOBALS['TL_LANG']['merger2']['mode'],
	'sql'       => 'varchar(14) NOT NULL default \'\'',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_template'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_template'],
	'default'   => 'merger_default',
	'inputType' => 'select',
	'options'   => $this->getTemplateGroup('merger_'),
	'eval'      => array('tl_class' => 'clr w50'),
	'sql'       => 'varchar(64) NOT NULL default \'merger_default\'',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_container'] = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_container'],
	'inputType' => 'checkbox',
	'eval'      => array('tl_class' => 'w50 m12'),
	'sql'       => 'char(1) NOT NULL default \'\'',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_data'] = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_data'],
	'inputType' => 'multiColumnWizard',
	'eval'      => array(
		'columnFields' => array
		(
			'content'   => array
			(
				'label'            => &$GLOBALS['TL_LANG']['tl_module']['merger_data_content'],
				'inputType'        => 'select',
				'options_callback' => array('tl_module_merger2', 'getModules'),
				'eval'             => array('style' => 'width:320px', 'includeBlankOption' => true, 'chosen' => true)
			),
			'condition' => array
			(
				'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_data_condition'],
				'exclude'   => true,
				'inputType' => 'text',
				'eval'      => array('style' => 'width:240px')
			),
			'disabled'  => array
			(
				'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_data_disabled'],
				'exclude'   => true,
				'inputType' => 'checkbox',
				'eval'      => array('style' => 'width:20px')
			),
			'edit'      => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_module']['merger_data_edit'],
				'input_field_callback' => array('tl_module_merger2', 'getEditButton')
			)
		)
	),
	'sql'       => 'text NULL',
);

class tl_module_merger2 extends \Backend
{
	public function onload(DataContainer $dc)
	{
		if (\Input::get('table') == 'tl_module' && \Input::get('act') == 'edit') {
			$module = \ModuleModel::findByPk($dc->id);
			if ($module && $module->type == 'Merger2') {
				$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/merger2/html/merger2.js';

				if (\Input::post('FORM_SUBMIT') == 'tl_module') {
					$blnDisabled = !\Input::post('merger_container');
				}
				else {
					$blnDisabled = !$module->merger_container;
				}

				$GLOBALS['TL_DCA']['tl_module']['fields']['cssID']['eval']['disabled'] = $blnDisabled;
				$GLOBALS['TL_DCA']['tl_module']['fields']['space']['eval']['disabled'] = $blnDisabled;
			}
		}
	}

	public function getModules($mcw)
	{
		// Get all modules from DB
		$modules = array(
			$GLOBALS['TL_LANG']['merger2']['legend_article']        => array(
				'article'                       => $GLOBALS['TL_LANG']['merger2']['article'],
				'inherit_articles'              => $GLOBALS['TL_LANG']['merger2']['inherit_articles'],
				'inherit_all_articles'          => $GLOBALS['TL_LANG']['merger2']['inherit_all_articles'],
				'inherit_articles_fallback'     => $GLOBALS['TL_LANG']['merger2']['inherit_articles_fallback'],
				'inherit_all_articles_fallback' => $GLOBALS['TL_LANG']['merger2']['inherit_all_articles_fallback']
			),
		);

		$themeCollection = \ThemeModel::findAll(array('order' => 'name'));
		while ($themeCollection->next()) {
			$modules[$themeCollection->name] = array();

			$moduleCollection = \ModuleModel::findBy('pid', $themeCollection->id, array('order' => 'name'));
			while ($moduleCollection->next()) {
				$modules[$themeCollection->name][$moduleCollection->id] = $moduleCollection->name;
			}
		}

		return $modules;
	}

	public function getEditButton($dc, $label)
	{
		$icon = \Image::getHtml('edit.gif');

		return sprintf('<a href="javascript:void(0);" class="edit_module">%s</a>', $icon);
	}
}
