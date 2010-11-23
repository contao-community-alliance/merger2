<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
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
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Merger2
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['Merger2'] = '{title_legend},name,headline,type;{config_legend},merger_mode,merger_template,merger_container,merger_data;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Add fields to tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_mode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['merger_mode'],
	'inputType'               => 'select',
	'options'                 => &$GLOBALS['TL_LANG']['merger2']['mode'],
	'eval'                    => array('tl_class'=>'w50')
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
	'eval'                    => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_data'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['merger_data'],
	'inputType'               => 'mergerModuleWizard',
	'eval'                    => array('tl_class'=>'clr')
);

?>