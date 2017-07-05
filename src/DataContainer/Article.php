<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG. 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\DataContainer;

/**
 * Class Article.
 */
class Article
{
    /**
     * Get active layout section.
     *
     * @param \DataContainer $dataContainer Data container.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getActiveLayoutSections(\DataContainer $dataContainer)
    {
        $callback = $GLOBALS['TL_DCA']['tl_article']['fields']['inColumn']['bit3_merger_original_options_callback'];

        if (is_array($callback)) {
            $object     = \System::importStatic($callback[0]);
            $methodName = $callback[1];
            $sections   = $object->$methodName($dataContainer);
        } else {
            $sections = call_user_func($callback, $dataContainer);
        }

        if ($dataContainer->activeRecord->pid) {
            $page = \PageModel::findWithDetails($dataContainer->activeRecord->pid);

            // Get the layout sections
            foreach (array('layout', 'mobileLayout') as $key) {
                if (!$page->$key) {
                    continue;
                }

                $layout = \LayoutModel::findByPk($page->$key);

                if ($layout === null) {
                    continue;
                }

                $modules = deserialize($layout->modules);

                if (empty($modules) || !is_array($modules)) {
                    continue;
                }

                // Find all sections with an article module (see #6094)
                foreach ($modules as $module) {
                    if ($module['mod'] != 0 && $module['enable']) {
                        $this->joinModule($module['col'], $module['mod'], $sections);
                    }
                }
            }
        }

        return array_values(array_unique($sections));
    }

    /**
     * Join modules.
     *
     * @param string $column   Column or section name.
     * @param int    $moduleId Module id.
     * @param array  $sections Sections.
     *
     * @return void
     */
    protected function joinModule($column, $moduleId, &$sections)
    {
        $module = \ModuleModel::findByPk($moduleId);

        if (!$module || $module->type != 'Merger2') {
            return;
        }

        $data = deserialize($module->merger_data, true);

        foreach ($data as $row) {
            if (!$row['disabled']) {
                if (in_array(
                    $row['content'],
                    array(
                        'article',
                        'inherit_articles',
                        'inherit_all_articles',
                        'inherit_articles_fallback',
                        'inherit_all_articles_fallback'
                    )
                )) {
                    $sections[] = $column;
                } elseif ($row['content']) {
                    $this->joinModule($column, $row['content'], $sections);
                }
            }
        }
    }
}
