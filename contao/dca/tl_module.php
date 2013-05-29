<?php

/**
 * Merger² - Module Merger
 * Copyright (C) 2011 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Merger²
 * @license    LGPL
 * @filesource
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
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['merger_mode'],
	'inputType'               => 'select',
	'options'                 => &$GLOBALS['TL_LANG']['merger2']['mode'],
);

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['merger_template'],
	'default'                 => 'merger_default',
	'inputType'               => 'select',
	'options'                 => $this->getTemplateGroup('merger_'),
	'eval'                    => array('tl_class'=>'clr w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_container'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['merger_container'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_data'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['merger_data'],
	'inputType'               => 'multiColumnWizard',
	'eval'                    => array(
		'columnFields' => array
		(
			'content' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_module']['merger_data_content'],
				'inputType'             => 'select',
				'options_callback'     	=> array('tl_module_merger2', 'getModules'),
				'eval' 			        => array('style' => 'width:320px', 'includeBlankOption'=>true, 'chosen'=>true)
			),
			'condition' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_module']['merger_data_condition'],
				'exclude'               => true,
				'inputType'             => 'text',
				'eval' 			=> array('style'=>'width:260px')
			),
			'edit' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_module']['merger_data_edit'],
				'input_field_callback' => array('tl_module_merger2', 'getEditButton')
			)
		)
	)
);

class tl_module_merger2 extends Backend
{
	public function onload(DataContainer $dc)
	{
		if ($this->Input->get('table') == 'tl_module' && $this->Input->get('act') == 'edit')
		{
			$objModule = $this->Database
				->prepare('SELECT * FROM tl_module WHERE id=?')
				->execute($dc->id);
			if ($objModule->next() && $objModule->type == 'Merger2')
			{
				$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/merger2/html/merger2.js';

				if ($this->Input->post('FORM_SUBMIT') == 'tl_module') {
					$blnDisabled = !$this->Input->post('merger_container');
				}
				else {
					$blnDisabled = !$objModule->merger_container;
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
			$GLOBALS['TL_LANG']['merger2']['legend_article'] => array(
				'article' => $GLOBALS['TL_LANG']['merger2']['article'],
				'inherit_articles' => $GLOBALS['TL_LANG']['merger2']['inherit_articles'],
				'inherit_all_articles' => $GLOBALS['TL_LANG']['merger2']['inherit_all_articles'],
				'inherit_articles_fallback' => $GLOBALS['TL_LANG']['merger2']['inherit_articles_fallback'],
				'inherit_all_articles_fallback' => $GLOBALS['TL_LANG']['merger2']['inherit_all_articles_fallback']
			),
			$GLOBALS['TL_LANG']['merger2']['legend_inherit_module'] => array(
				'inherit_modules' => $GLOBALS['TL_LANG']['merger2']['inherit_modules'],
				'inherit_all_modules' => $GLOBALS['TL_LANG']['merger2']['inherit_all_modules']
			)
		);

		$objTheme = $this->Database
			->execute("SELECT * FROM tl_theme ORDER BY name");
		while ($objTheme->next())
		{
			$modules[$objTheme->name] = array();

			$objModules = $this->Database
				->prepare("SELECT id, name FROM tl_module WHERE pid=? AND id!=? ORDER BY name")
				->execute((int) $objTheme->id, $mcw ? $mcw->currentRecord : 0);
			while ($objModules->next())
			{
				$modules[$objTheme->name][$objModules->id] = $objModules->name;
			}
		}

		return $modules;
	}

	public function getEditButton($dc, $label)
	{
		$icon = $this->generateImage('edit.gif', '');

		return sprintf('<a href="javascript:void(0);" class="edit_module">%s</a>', $icon);
	}
}
