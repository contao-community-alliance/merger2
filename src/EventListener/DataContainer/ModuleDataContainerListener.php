<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @author    Ingolf Steinhardt <info@e-spin.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2022 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\Model\Collection;
use Contao\ModuleModel;
use Contao\ThemeModel;

use function assert;

/**
 * Module data container helper class.
 *
 * @package ContaoCommunityAlliance\Merger2\DataContainer
 */
final class ModuleDataContainerListener
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
    public function onload(DataContainer $dataContainer): void
    {
        if (Input::get('table') === 'tl_module' && Input::get('act') === 'edit') {
            $module = ModuleModel::findByPk($dataContainer->id);
            if ($module && $module->type === 'Merger2') {
                $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/ccamerger2/merger2.js';
            }
        }
    }

    /**
     * Get article content and all available modules.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getModules(): array
    {
        // Get article content.
        $modules = array(
            $GLOBALS['TL_LANG']['merger2']['legend_article']        => array(
                'article'                       => $GLOBALS['TL_LANG']['merger2']['article'],
                'inherit_articles'              => $GLOBALS['TL_LANG']['merger2']['inherit_articles'],
                'inherit_all_articles'          => $GLOBALS['TL_LANG']['merger2']['inherit_all_articles'],
                'inherit_articles_fallback'     => $GLOBALS['TL_LANG']['merger2']['inherit_articles_fallback'],
                'inherit_all_articles_fallback' => $GLOBALS['TL_LANG']['merger2']['inherit_all_articles_fallback']
            ),
        );

        // Get all modules from DB.
        $themeCollection = ThemeModel::findAll(array('order' => 'name'));
        assert($themeCollection instanceof Collection || $themeCollection === null);
        foreach ($themeCollection ?: [] as $theme) {
            $modules[$theme->name] = array();

            $moduleCollection = ModuleModel::findBy('pid', $theme->id, array('order' => 'name'));
            assert($moduleCollection instanceof Collection || $moduleCollection === null);
            foreach ($moduleCollection ?: [] as $module) {
                $category = sprintf(
                    $GLOBALS['TL_LANG']['merger2']['legend_module'],
                    $theme->name,
                    $module->pid
                );

                $modules[$category][$module->id] = $module->name;
            }
        }

        return $modules;
    }

    /**
     * Get the edit button.
     *
     * @return string
     */
    public function getEditButton(): string
    {
        $icon = Image::getHtml('edit.gif');

        return sprintf('<a href="javascript:void(0);" class="edit_module">%s</a>', $icon);
    }
}
