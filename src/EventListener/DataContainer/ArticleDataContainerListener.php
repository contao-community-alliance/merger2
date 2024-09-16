<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2022 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;

/**
 * Class Article.
 */
final class ArticleDataContainerListener
{
    /**
     * Get active layout section.
     *
     * @param DataContainer $dataContainer Data container.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getActiveLayoutSections(DataContainer $dataContainer): array
    {
        $sections = $this->callOriginalActiveLayoutSectionsCallback($dataContainer);

        if (!$dataContainer->activeRecord || !$dataContainer->activeRecord->pid) {
            return $sections;
        }

        $page = PageModel::findWithDetails($dataContainer->activeRecord->pid);

        // Get the layout sections
        foreach (['layout', 'mobileLayout'] as $key) {
            if (!$page->$key) {
                continue;
            }

            $layout = LayoutModel::findByPk($page->$key);
            $this->joinLayoutModules($layout, $sections);
        }

        return $sections;
    }

    /**
     * Call the original active layout sections callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function callOriginalActiveLayoutSectionsCallback(DataContainer $dataContainer)
    {
        $callback = $GLOBALS['TL_DCA']['tl_article']['fields']['inColumn']['merger_original_options_callback'];

        if (is_array($callback)) {
            $object     = System::importStatic($callback[0]);
            $methodName = $callback[1];
            $sections   = $object->$methodName($dataContainer);
        } elseif (is_callable($callback)) {
            $sections = call_user_func($callback, $dataContainer);
        } else {
            $sections = [];
        }

        return $sections;
    }

    /**
     * Joint modules of a layout.
     *
     * @param LayoutModel|null $layout   Layout model.
     * @param array            $sections Sections.
     *
     * @return void
     */
    private function joinLayoutModules(?LayoutModel $layout, array &$sections): void
    {
        if ($layout === null) {
            return;
        }

        $modules = StringUtil::deserialize($layout->modules);

        if (empty($modules) || !is_array($modules)) {
            return;
        }

        // Find all sections with an article module (see #6094)
        foreach ($modules as $module) {
            if ($module['mod'] !== '0' && ($module['enable'] ?? false)) {
                $this->joinModule($module['col'], $module['mod'], $sections);
            }
        }
    }

    /**
     * Join module.
     *
     * @param string $column   Column or section name.
     * @param int    $moduleId Module id.
     * @param array  $sections Sections.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function joinModule($column, $moduleId, &$sections)
    {
        $module = ModuleModel::findByPk($moduleId);

        if (!$module || $module->type !== 'Merger2') {
            return;
        }

        $data = StringUtil::deserialize($module->merger_data, true);

        foreach ($data as $row) {
            if (!$row['disabled']) {
                if (in_array(
                    $row['content'],
                    [
                        'article',
                        'inherit_articles',
                        'inherit_all_articles',
                        'inherit_articles_fallback',
                        'inherit_all_articles_fallback',
                    ]
                )) {
                    $sections[$column] = isset($GLOBALS['TL_LANG']['COLS'][$column])
                        ? $GLOBALS['TL_LANG']['COLS'][$column]
                        : $column;
                } elseif ($row['content']) {
                    $this->joinModule($column, $row['content'], $sections);
                }
            }
        }
    }
}
