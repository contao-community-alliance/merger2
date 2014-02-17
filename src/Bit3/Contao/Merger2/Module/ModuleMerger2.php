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

namespace Bit3\Contao\Merger2\Module;

use Bit3\Contao\Merger2\Constraint\Parser\InputStream;
use Bit3\Contao\Merger2\Constraint\Parser\Parser;

/**
 * Class ModuleMerger2
 */
class ModuleMerger2 extends \Module
{
	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'mod_merger2';

	/**
	 * Generate a front end module and return it as HTML string
	 *
	 * @param integer
	 * @param string
	 *
	 * @return string
	 */
	protected function getPageFrontendModule($page, $moduleId, $columnName = 'main', $inheritableOnly = false)
	{
		if (!is_object($moduleId) && !strlen($moduleId)) {
			return '';
		}

		// Articles
		if ($moduleId == 0) {
			// Show a particular article only
			if ($page->type == 'regular' && \Input::get('articles')) {
				list($sectionName, $articleName) = explode(':', \Input::get('articles'));

				if ($articleName === null) {
					$articleName = $sectionName;
					$sectionName = 'main';
				}

				if ($sectionName == $columnName) {
					$article = \ArticleModel::findByIdOrAliasAndPid($articleName, $page->id);

					// Send a 404 header if the article does not exist
					if ($article === null) {
						// Do not index the page
						$page->noSearch = 1;
						$page->cache    = 0;

						header('HTTP/1.1 404 Not Found');
						return '<p class="error">' . sprintf(
							$GLOBALS['TL_LANG']['MSC']['invalidPage'],
							$articleName
						) . '</p>';
					}

					if (!$inheritableOnly || $article->inheritable) {
						// Add the "first" and "last" classes (see #2583)
						$article->classes = array('first', 'last');

						return $this->getArticle($article);
					}

					return '';
				}
			}

			// HOOK: trigger the article_raster_designer extension
			if (in_array('article_raster_designer', \ModuleLoader::getActive())) {
				return \RasterDesigner::load($page->id, $columnName);
			}

			// Show all articles (no else block here, see #4740)
			$articleCollection = \ArticleModel::findPublishedByPidAndColumn($page->id, $columnName);

			if ($articleCollection === null) {
				return '';
			}

			$return       = '';
			$intCount     = 0;
			$blnMultiMode = ($articleCollection->count() > 1);
			$intLast      = $articleCollection->count() - 1;

			while ($articleCollection->next()) {
				if ($inheritableOnly && !$articleCollection->inheritable) {
					continue;
				}

				$articleRow = $articleCollection->current();

				// Add the "first" and "last" classes (see #2583)
				if ($intCount == 0 || $intCount == $intLast) {
					$cssClasses = array();

					if ($intCount == 0) {
						$cssClasses[] = 'first';
					}

					if ($intCount == $intLast) {
						$cssClasses[] = 'last';
					}

					$articleRow->classes = $cssClasses;
				}

				$return .= $this->getArticle($articleRow, $blnMultiMode, false, $columnName);
				++$intCount;
			}

			return $return;
		}

		// Other modules
		else {
			if (is_object($moduleId)) {
				$articleRow = $moduleId;
			}
			else {
				$articleRow = \ModuleModel::findByPk($moduleId);

				if ($articleRow === null) {
					return '';
				}
			}

			// Check the visibility (see #6311)
			if (!static::isVisibleElement($articleRow)) {
				return '';
			}

			$moduleClassName = \Module::findClass($articleRow->type);

			// Return if the class does not exist
			if (!class_exists($moduleClassName)) {
				$this->log(
					'Module class "' . $moduleClassName . '" (module "' . $articleRow->type . '") does not exist',
					__METHOD__,
					TL_ERROR
				);
				return '';
			}

			$articleRow->typePrefix = 'mod_';
			/** @var \Module $module */
			$module = new $moduleClassName($articleRow, $columnName);
			$buffer = $module->generate();

			// HOOK: add custom logic
			if (isset($GLOBALS['TL_HOOKS']['getFrontendModule']) && is_array(
					$GLOBALS['TL_HOOKS']['getFrontendModule']
				)
			) {
				foreach ($GLOBALS['TL_HOOKS']['getFrontendModule'] as $callback) {
					$this->import($callback[0]);
					$buffer = $this->$callback[0]->$callback[1]($articleRow, $buffer, $module);
				}
			}

			// Disable indexing if protected
			if ($module->protected && !preg_match('/^\s*<!-- indexer::stop/', $buffer)) {
				$buffer = "\n<!-- indexer::stop -->" . $buffer . "<!-- indexer::continue -->\n";
			}

			return $buffer;
		}
	}

	/**
	 * Generate an article and return it as string
	 *
	 * @param integer
	 * @param boolean
	 * @param boolean
	 * @param string
	 *
	 * @return string
	 */
	protected function getPageArticle(
		$page,
		$articleId
	) {
		$article = \ArticleModel::findByIdOrAliasAndPid($articleId, $page->id);

		if ($article === null) {
			return '';
		}

		return $this->getArticle($article);
	}

	/**
	 * Inherit article from parent page
	 *
	 * @param \PageModel $page
	 * @param int        $maxLevel
	 * @param int        $currentLevel
	 */
	protected function inheritArticle($page, $maxLevel = 0, $currentLevel = 0)
	{
		$parentPage = \PageModel::findPublishedById($page->pid);

		if ($parentPage === null) {
			return '';
		}

		$html = $this->getPageFrontendModule($parentPage, 0, $this->strColumn, true);
		if ($maxLevel == 0 || $maxLevel < ++$currentLevel) {
			$html .= $this->inheritArticle($parentPage, $maxLevel, $currentLevel);
		}
		return $html;
	}

	/**
	 * Mode is "all"
	 *
	 * @return bool
	 */
	protected function isModeAll()
	{
		return $this->merger_mode == 'all';
	}

	/**
	 * Mode is "up first false"
	 *
	 * @return bool
	 */
	protected function isModeUpFirstFalse()
	{
		return $this->merger_mode == 'upFirstFalse';
	}

	/**
	 * Mode is "up first true"
	 *
	 * @return bool
	 */
	protected function isModeUpFirstTrue()
	{
		return $this->merger_mode == 'upFirstTrue';
	}


	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE') {
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### MERGER2 ###';
			$objTemplate->title    = $this->headline;
			$objTemplate->id       = $this->id;
			$objTemplate->link     = $this->name;
			$objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// generate the merger container
		if ($this->merger_container) {
			return parent::generate();
		}

		// or only the content
		else {
			return $this->generateContent();
		}
	}

	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->Template->content = $this->generateContent();
	}

	/**
	 * Generate content
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
	 */
	protected function generateContent()
	{
		$modules = deserialize($this->merger_data);
		$buffer  = '';

		foreach ($modules as $module) {
			if ($module['disabled']) {
				continue;
			}

			$result    = null;
			$condition = trim(html_entity_decode($module['condition']));
			if (strlen($condition)) {
				$input  = new InputStream($condition);
				$parser = new Parser();
				$node   = $parser->parse($input);
				$result = $node->evaluate();
			}

			if ($result || $result === null) {
				$content = '';
				switch ($module['content']) {
					case '-':
						break;

					/**
					 * Include the articles from current page.
					 */
					case 'article':
						$content = $this->getPageFrontendModule(
							$GLOBALS['objPage'],
							0,
							$this->strColumn
						);
						break;

					/**
					 * Inherit articles from one upper level that contains articles.
					 */
					case 'inherit_articles':
						$content = $this->inheritArticle(
							$GLOBALS['objPage'],
							1
						);
						break;

					/**
					 * Inherit articles from all upper levels.
					 */
					case 'inherit_all_articles':
						$content = $this->inheritArticle(
							$GLOBALS['objPage']
						);
						break;

					/**
					 * Include the articles from current page or inherit from one upper level that contains articles.
					 */
					case 'inherit_articles_fallback':
						$content = $this->getPageFrontendModule(
							$GLOBALS['objPage'],
							0,
							$this->strColumn
						);

						if (!strlen($content)) {
							$content = $this->inheritArticle($GLOBALS['objPage'], 1);
						}
						break;

					/**
					 * Include the articles from current page or inherit from upper all upper levels.
					 */
					case 'inherit_all_articles_fallback':
						$content = $this->getPageFrontendModule(
							$GLOBALS['objPage'],
							0,
							$this->strColumn
						);

						if (!strlen($content)) {
							$content = $this->inheritArticle($GLOBALS['objPage']);
						}
						break;

					/**
					 * Include a module.
					 */
					default:
						$content = $this->getPageFrontendModule(
							$GLOBALS['objPage'],
							$module['content'],
							$this->strColumn
						);
				}

				$buffer .= $content;
				if ($result === null) {
					$result = strlen($content) > 0;
				}
			}
			if ($result && $this->isModeUpFirstTrue() || !$result && $this->isModeUpFirstFalse()) {
				break;
			}
		}

		$tpl          = new \FrontendTemplate($this->merger_template);
		$tpl->content = $buffer;
		return $tpl->parse();
	}
}
