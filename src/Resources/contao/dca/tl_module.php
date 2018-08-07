<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    Sven Baumann <baumann.sv@googlemail.com>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @author    Ingolf Steinhardt <info@e-spin.de>
 * @copyright 2013-2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

use ContaoCommunityAlliance\Merger2\EventListener\DataContainer\ModuleDataContainerListener;

/*
 * Table tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = [
    ModuleDataContainerListener::class,
    'onload',
];


/*
 * Add palettes to tl_module
 */

/** @codingStandardsIgnoreStart */
$GLOBALS['TL_DCA']['tl_module']['palettes']['Merger2'] = '{title_legend},name,headline,type'
    . ';{config_legend},merger_mode,merger_data'
    . ';{template_legend:hide},merger_template,merger_container'
    . ';{protected_legend:hide},protected'
    . ';{expert_legend:hide},guests,cssID,space';
/** @codingStandardsIgnoreEnd */

/*
 * Add fields to tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_mode'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_mode'],
    'inputType' => 'select',
    'options'   => &$GLOBALS['TL_LANG']['merger2']['mode'],
    'sql'       => 'varchar(14) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_template'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_template'],
    'default'   => 'merger_default',
    'inputType' => 'select',
    'options'   => $this->getTemplateGroup('merger_'),
    'eval'      => ['tl_class' => 'clr w50'],
    'sql'       => 'varchar(64) NOT NULL default \'merger_default\'',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_container'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_container'],
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => 'char(1) NOT NULL default \'\'',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['merger_data'] = [
    'label'       => &$GLOBALS['TL_LANG']['tl_module']['merger_data'],
    'inputType'   => 'multiColumnWizard',
    'explanation' => 'merger2Functions',
    'eval'        => [
        'columnFields' => [
            'content'   => [
                'label'            => &$GLOBALS['TL_LANG']['tl_module']['merger_data_content'],
                'inputType'        => 'select',
                'options_callback' => [ModuleDataContainerListener::class, 'getModules'],
                'eval'             => ['style' => 'width:320px', 'includeBlankOption' => true, 'chosen' => true],
            ],
            'condition' => [
                'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_data_condition'],
                'exclude'   => true,
                'inputType' => 'text',
                'eval'      => ['style' => 'width:240px', 'allowHtml' => true, 'preserveTags' => true],
            ],
            'disabled'  => [
                'label'     => &$GLOBALS['TL_LANG']['tl_module']['merger_data_disabled'],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => ['style' => 'width:20px'],
            ],
            'edit'      => [
                'label'                => &$GLOBALS['TL_LANG']['tl_module']['merger_data_edit'],
                'input_field_callback' => [ModuleDataContainerListener::class, 'getEditButton'],
            ],
        ],
        'dragAndDrop'  => true,
        'helpwizard'   => true,
    ],
    'sql'         => 'text NULL',
];
