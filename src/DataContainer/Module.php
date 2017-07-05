<?php

/**
 * @package    merger2
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace ContaoCommunityAlliance\Merger2\DataContainer;

use Contao\Backend;
use Contao\DataContainer;

class Module extends Backend
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
            if ($moduleCollection) {
                while ($moduleCollection->next()) {
                    $modules[$themeCollection->name][$moduleCollection->id] = $moduleCollection->name;
                }
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
