<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * @copyright 2013,2014 bit3 UG
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    Sven Baumann <baumann.sv@googlemail.com>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @link      http://bit3.de
 * @package   bit3/contao-merger2
 * @license   LGPL-3.0+
 */


/**
 * Table tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('ContaoCommunityAlliance\Merger2\DataContainer\Module', 'onload');


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
				'options_callback' => array('ContaoCommunityAlliance\Merger2\DataContainer\Module', 'getModules'),
				'eval'             => array('style' => 'width:320px', 'includeBlankOption' => true, 'chosen' => true)
			),
			'condition' => array
			(
				'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_data_condition'],
				'exclude'   => true,
				'inputType' => 'text',
				'eval'      => array('style' => 'width:240px', 'allowHtml' => true, 'preserveTags' => true)
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
				'input_field_callback' => array('ContaoCommunityAlliance\Merger2\DataContainer\Module', 'getEditButton')
			)
		)
	),
	'sql'       => 'text NULL',
);
