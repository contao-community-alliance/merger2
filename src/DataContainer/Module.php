<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\DataContainer;

use Contao\Backend;
use Contao\DataContainer;

/**
 * Module data container helper class.
 *
 * @package ContaoCommunityAlliance\Merger2\DataContainer
 */
class Module extends Backend
{
    /**
     * Onload callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function onload(DataContainer $dataContainer)
    {
        if (\Input::get('table') == 'tl_module' && \Input::get('act') == 'edit') {
            $module = \ModuleModel::findByPk($dataContainer->id);
            if ($module && $module->type == 'Merger2') {
                $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/ccamerger2/merger2.js';
            }
        }
    }

    /**
     * Get all available modules.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getModules()
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
            if ($moduleCollection) {
                while ($moduleCollection->next()) {
                    $modules[$themeCollection->name][$moduleCollection->id] = $moduleCollection->name;
                }
            }
        }

        return $modules;
    }

    /**
     * Get the edit button.
     *
     * @return string
     */
    public function getEditButton()
    {
        $icon = \Image::getHtml('edit.gif');

        return sprintf('<a href="javascript:void(0);" class="edit_module">%s</a>', $icon);
    }
}
