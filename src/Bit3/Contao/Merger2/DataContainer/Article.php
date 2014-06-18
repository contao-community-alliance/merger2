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

namespace Bit3\Contao\Merger2\DataContainer;

/**
 * Class Article
 */
class Article extends \tl_article
{
	public function getActiveLayoutSections(\DataContainer $dc)
	{
		$sections = parent::getActiveLayoutSections($dc);

		if ($dc->activeRecord->pid) {
			$page = \PageModel::findWithDetails($dc->activeRecord->pid);

			// Get the layout sections
			foreach (array('layout', 'mobileLayout') as $key)
			{
				if (!$page->$key)
				{
					continue;
				}

				$layout = \LayoutModel::findByPk($page->$key);

				if ($layout === null)
				{
					continue;
				}

				$modules = deserialize($layout->modules);

				if (empty($modules) || !is_array($modules))
				{
					continue;
				}

				// Find all sections with an article module (see #6094)
				foreach ($modules as $module)
				{
					if ($module['mod'] <> 0 && $module['enable'])
					{
						$this->joinModule($module['col'], $module['mod'], $sections);
					}
				}
			}
		}

		return array_values(array_unique($sections));
	}

	protected function joinModule($column, $moduleId, &$sections)
	{
		$module = \ModuleModel::findByPk($moduleId);

		if (!$module || $module->type != 'Merger2') {
			return;
		}

		$data = deserialize($module->merger_data, true);

		foreach ($data as $row) {
			if (!$row['disabled']) {
				if (in_array($row['content'], array('article', 'inherit_articles', 'inherit_all_articles', 'inherit_articles_fallback', 'inherit_all_articles_fallback'))) {
					$sections[] = $column;
				}
				else if ($row['content']) {
					$this->joinModule($column, $row['content'], $sections);
				}
			}
		}
	}
}
