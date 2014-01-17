<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG
 *
 * @package merger2
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @link    http://bit3.de
 * @license LGPL-3.0+
 */


/**
 * Class ModuleMerger2
 */
class ModuleMerger2 extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_merger2';


	/**
	 * function: language(..)
	 * Test the page language.
	 * @param mixed $strLanguage
	 * @return boolean
	 */
	private function language($strLanguage) {
		global $objPage;
		return (strtolower($objPage->language) == strtolower($strLanguage));
	}


	/**
	 * function: page(..)
	 * Test the page id or alias.
	 * @param mixed $strId
	 * @return boolean
	 */
	private function page($strId) {
		global $objPage;
		return (intval($strId) == $objPage->id || $strId == $objPage->alias) ? true : false;
	}


	/**
	 * function: root(..)
	 * Test the root page id or alias.
	 * @param mixed $strId
	 * @return boolean
	 */
	private function root($strId) {
		global $objPage;
		return (intval($strId) == $objPage->rootId || $strId == $this->getPageDetails($objPage->rootId)->alias) ? true : false;
	}


	/**
	 * function: pageInPath(..)
	 * Test if page id or alias is in path.
	 * @param mixed $strId
	 * @return boolean
	 */
	private function pageInPath($strId) {
		$page = $GLOBALS['objPage'];
		while (true) {
			if (intval($strId) == $page->id || $strId == $page->alias)
			{
				return true;
			}
			if ($page->pid > 0) {
				$page = $this->getPageDetails($page->pid);
			} else {
				return false;
			}
		}
	}

	/**
	 * Evaluate value to bool.
	 * @param string $strValue
	 * @return boolean
	 */
	private function boolean($strValue) {
		switch (strtolower($strValue)) {
		case 'true': case '1': case '!false': case '!0': return true;
		case 'false': case '0': case '!true': case '!1': return false;
		default: throw new Exception('Illegal boolean value: "' . $strValue . '"');
		}
	}

	/**
	 * function: depth(..)
	 * Test the page depth.
	 * @param mixed $strValue
	 * @return boolean
	 */
	private function depth($strValue) {
		if (preg_match('#^(<|>|<=|>=|=|!=)?\\s*(\\d+)$#', $strValue, $m)) {
			$cmp = $m[1] ? $m[1] : '=';
			$i = intval($m[2]);

			$n = 0;
			$page = $this->getPageDetails($GLOBALS['objPage']->id);
			while ($page->pid > 0 && $page->type != 'root') {
				$n ++;
				$page = $this->getPageDetails($page->pid);
			}

			switch ($cmp) {
			case '<': return $n < $i;
			case '>': return $n > $i;
			case '<=': return $n <= $i;
			case '>=': return $n >= $i;
			case '=': return $n == $i;
			case '!=': return $n != $i;
			}
		} else {
			throw new Exception('Illegal depth value: "' . $strValue . '"');
		}
	}

	/**
	 * function: articleExists(..)
	 * Test if an article exists in the column.
	 * @param string $strColumn
	 * @param boolean $boolIncludeUnpublished
	 * @return boolean;
	 */
	function articleExists($strColumn, $boolIncludeUnpublished = false) {
		global $objPage;
		$time = time();
		$objArticle = $this->Database->prepare("SELECT COUNT(id) as count FROM tl_article WHERE pid=? AND inColumn=?" .
										($boolIncludeUnpublished ? '' : " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1"))
									 ->limit(1)
									 ->execute($objPage->id, $strColumn, $time, $time);
		if ($objArticle->next())
			return $objArticle->count > 0;
		else
			return false;
	}

	/**
	 * function: children(..)
	 * Test if the page have the specific count of children.
	 * @param integer $intCount
	 * @param boolean $boolIncludeUnpublished
	 * @return boolean
	 */
	function children($intCount, $boolIncludeUnpublished = false) {
		global $objPage;
		$time = time();
		$objChildren = $this->Database->prepare("SELECT COUNT(id) as count FROM tl_page WHERE pid=?" .
										($boolIncludeUnpublished ? '' : " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1"))
									 ->limit(1)
									 ->execute($objPage->id, $time, $time);
		if ($objChildren->next())
			return $objChildren->count >= $intCount;
		return false;
	}

	/**
	 * function: platform(..)
	 * @param integer $intCount
	 * @param boolean $boolIncludeUnpublished
	 * @return boolean
	 */
	function platform($platform) {
		if (in_array('theme-plus', \Config::getInstance()->getActiveModules())) {
			return \Bit3\Contao\ThemePlus\ThemePlus::checkFilter(
				null,
				null,
				null,
				null,
				$platform
			);
		}
		else {
			$mobileDetect = new \Mobile_Detect();

			switch ($platform) {
				case 'desktop':
					return !$mobileDetect->isMobile();
				case 'tablet':
					return $mobileDetect->isTablet();
				case 'smartphone':
					return !$mobileDetect->isTablet() && $mobileDetect->isMobile();
				case 'mobile':
					return $mobileDetect->isMobile();
				default:
					return false;
			}
		}
	}

	/**
	 * Evaluate a function to bool result.
	 * @param array $matches
	 * @return boolean
	 */
	public function evaluateFunctionBool($matches) {
		$args = explode(',', $matches[2]);
		switch ($matches[1]) {
			case 'language': return $this->language(trim($args[0]));
			case 'page': return $this->page(trim($args[0]));
			case 'root': return $this->root(trim($args[0]));
			case 'pageInPath': return $this->pageInPath(trim($args[0]));
			case 'depth': return $this->depth(trim($args[0]));
			case 'articleExists': return $this->articleExists(isset($args[0]) ? trim($args[0]) : 'main');
			case 'articleExistsReal': return $this->articleExists(isset($args[0]) ? trim($args[0]) : 'main', true);
			case 'children': return $this->children(isset($args[0]) && is_numeric($args[0]) ? trim($args[0]) : 1);
			case 'childrenReal': return $this->children(isset($args[0]) && is_numeric($args[0]) ? trim($args[0]) : 1, true);
			case 'platform': return $this->platform($args[0]);
			default: return call_user_func_array(trim($matches[1]), $args);
		}
	}

	/**
	 * Evaluate a function to string result.
	 * @param array $matches
	 * @return string
	 */
	public function evaluateFunction($matches) {
		return $this->evaluateFunctionBool($matches) ? 'true' : 'false';
	}

	/**
	 * Evaluate AND concatenation.
	 * @param array $matches
	 * @return string
	 */
	public function evaluateAnd($matches) {
		$left = $this->boolean($matches[1]);
		$right = $this->boolean($matches[3]);
		return $left && $right ? 'true' : 'false';
	}

	/**
	 * Evaluate OR concatenation.
	 * @param array $matches
	 * @return string
	 */
	public function evaluateOr($matches) {
		$left = $this->boolean($matches[1]);
		$right = $this->boolean($matches[3]);
		return $left || $right ? 'true' : 'false';
	}

	/**
	 * Evaluate a block.
	 * @param array $matches
	 * @return string
	 */
	public function evaluateBlock($matches) {
		return $this->evaluate(trim($matches[1])) ? 'true' : 'false';
	}

	/**
	 * Evaluate an expression.
	 * @param string $expression
	 * @return boolean
	 */
	private function evaluate($expression) {
		$intCount = 0;
		// evaluate all functions
		do
		{
			$expression = preg_replace_callback(
				'#([\\w\\\\:]+)\\(([^\\)]*)\\)#',
				array(&$this, 'evaluateFunction'),
				$expression,
				-1,
				$intCount);
		}
		while($intCount > 0);
		// evaluate blocks
		do
		{
			$expression = preg_replace_callback(
				'#\\(([^\\)]+)\\)#',
				array(&$this, 'evaluateBlock'),
				$expression,
				-1,
				$intCount);
		}
		while ($intCount > 0);
		// evaluate 'and'-concatenations
		do
		{
			$expression = preg_replace_callback(
				'#(!?true|!?false|!?0|!?1)\\s+(and|&)\\s+(!?true|!?false|!?0|!?1)#i',
				array(&$this, 'evaluateAnd'),
				$expression,
				-1,
				$intCount);
		}
		while($intCount > 0);
		// evaluate 'or'-concatenations
		do
		{
			$expression = preg_replace_callback(
				'#(!?true|!?false|!?0|!?1)\\s+(or|\\|)\\s+(!?true|!?false|!?0|!?1)#i',
				array(&$this, 'evaluateOr'),
				$expression,
				-1,
				$intCount);
		}
		while($intCount > 0);
		// return a boolean result
		return $this->boolean($expression);
	}

	/**
	 * Generate a front end module and return it as HTML string
	 * @param integer
	 * @param string
	 * @return string
	 */
	protected function getPageFrontendModule(&$objPage, $intId, $strColumn='main', $onlyInheritable = false)
	{
		$this->import('Database');

		if (!strlen($intId))
		{
			return '';
		}

		// Articles
		if ($intId == 0)
		{
			// Show a particular article only
			if ($this->Input->get('articles') && $objPage->type == 'regular')
			{
				list($strSection, $strArticle) = explode(':', $this->Input->get('articles'));

				if (is_null($strArticle))
				{
					$strArticle = $strSection;
					$strSection = 'main';
				}

				if ($strSection == $strColumn)
				{
					return $this->getPageArticle($objPage, $strArticle);
				}
			}

			// HOOK: trigger article_raster_designer extension
			elseif (in_array('article_raster_designer', $this->Config->getActiveModules()))
			{
				return RasterDesigner::load($objPage->id, $strColumn);
			}

			$time = time();

			// Show all articles of the current column
			$objArticles = $this->Database->prepare("SELECT id FROM tl_article WHERE pid=? AND inColumn=?" . ($onlyInheritable ? " AND inheritable=1" : "") . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) AND published=1" : "") . " ORDER BY sorting")
										  ->execute($objPage->id, $strColumn);

			if (($intCount = $objArticles->numRows) < 1)
			{
				return '';
			}

			$return = '';

			while ($objArticles->next())
			{
				$return .= $this->getPageArticle($objPage, $objArticles->id, (($intCount > 1) ? true : false), false, $strColumn);
			}

			return $return;
		}

		// Other modules
		if (version_compare(VERSION, '3', '<')) {
			$objModule = $this->Database->prepare("SELECT * FROM tl_module WHERE id=?")
										->limit(1)
										->execute($intId);

			if ($objModule->numRows < 1)
			{
				return '';
			}
		}
		else {
			$objModule = \ModuleModel::findByPK($intId);

			if ($objModule === null)
			{
				return '';
			}
		}

		// Show to guests only
		if ($objModule->guests && FE_USER_LOGGED_IN && !BE_USER_LOGGED_IN && !$objModule->protected)
		{
			return '';
		}

		// Protected element
		if (!BE_USER_LOGGED_IN && $objModule->protected)
		{
			if (!FE_USER_LOGGED_IN)
			{
				return '';
			}

			$this->import('FrontendUser', 'User');
			$arrGroups = deserialize($objModule->groups);

			if (is_array($arrGroups) && count(array_intersect($this->User->groups, $arrGroups)) < 1)
			{
				return '';
			}
		}

		$strClass = $this->findFrontendModule($objModule->type);

		if (!$this->classFileExists($strClass))
		{
			$this->log('Module class "'.$strClass.'" (module "'.$objModule->type.'") does not exist', 'Controller getFrontendModule()', TL_ERROR);
			return '';
		}

		$objModule->typePrefix = 'mod_';
		$objModule = new $strClass($objModule, $strColumn);


		$strBuffer = $objModule->generate();

		// Disable indexing if protected
		if ($objModule->protected && !preg_match('/^\s*<!-- indexer::stop/i', $strBuffer))
		{
			$strBuffer = "\n<!-- indexer::stop -->$strBuffer<!-- indexer::continue -->\n";
		}

		return $strBuffer;
	}


	/**
	 * Generate an article and return it as string
	 *
	 * @param integer
	 * @param boolean
	 * @param boolean
	 * @param string
	 * @return string
	 */
	protected function getPageArticle(&$objPage, $varId, $boolMultiMode=false, $boolIsInsertTag=false, $strColumn='main')
	{
		if (!$varId)
		{
			return '';
		}

		$this->import('Database');

		// Get article
		$objArticle = $this->Database->prepare("SELECT *, author AS authorId, (SELECT name FROM tl_user WHERE id=author) AS author FROM tl_article WHERE (id=? OR alias=?)" . (!$boolIsInsertTag ? " AND pid=?" : ""))
									 ->limit(1)
									 ->execute((is_numeric($varId) ? $varId : 0), $varId, $objPage->id);

		if ($objArticle->numRows < 1)
		{
			return '';
		}

		if (version_compare(VERSION, '3', '<') &&
            !file_exists(TL_ROOT . '/system/modules/frontend/ModuleArticle.php'))
		{
			$this->log('Class ModuleArticle does not exist', 'Controller getArticle()', TL_ERROR);
			return '';
		}

		// Print article as PDF
		if ($this->Input->get('pdf') == $objArticle->id)
		{
			$this->printArticleAsPdf($objArticle);
		}

		$objArticle->headline = $objArticle->title;
		$objArticle->multiMode = $boolMultiMode;

		$objArticle = new ModuleArticle($objArticle, $strColumn);
		return $objArticle->generate($boolIsInsertTag);
	}


	/**
	 * Inherit article from parent page
	 */
	protected function inheritArticle(&$objPage, $intMax = 0, $intLevel = 0) {
		$objParent = $this->Database->prepare('
				SELECT
					*
				FROM
					tl_page
				WHERE
					id = ?')
			->limit(1)
			->execute($objPage->pid);
		if ($objParent->next()) {
			$html = $this->getPageFrontendModule($objParent, 0, $this->strColumn, true);
			if ($intMax == 0 || $intMax < ++$intLevel)
				$html .= $this->inheritArticle($objParent, $intMax, $intLevel);
			return $html;
		}
		return '';
	}


	/**
	 * Mode is "all"
	 */
	protected function isModeAll() {
		return $this->merger_mode == 'all';
	}


	/**
	 * Mode is "up first false"
	 */
	protected function isModeUpFirstFalse() {
		return $this->merger_mode == 'upFirstFalse';
	}


	/**
	 * Mode is "up first true"
	 */
	protected function isModeUpFirstTrue() {
		return $this->merger_mode == 'upFirstTrue';
	}


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate() {
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### MERGER2 ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// generate the merger container
		if ($this->merger_container)
		{
			return parent::generate();
		}

		// or only the content
		else
		{
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
	 */
	protected function generateContent() {
		$tpl = new FrontendTemplate($this->merger_template);

		$modules = deserialize($this->merger_data);
		$tpl->content = '';
		foreach ($modules as $module) {
			if ($module['disabled']) {
				continue;
			}

			$result = null;
			$condition = trim(html_entity_decode($module['condition']));
			if (strlen($condition)) {
				$result = $this->evaluate($condition);
			}

			if ($result || $result === null) {
				$content = '';
				switch ($module['content']) {
				case '-':
					break;

				case 'article':
					$content = $this->getPageFrontendModule($GLOBALS['objPage'], 0, $this->strColumn);
					break;

				case 'inherit_articles':
					$content = $this->inheritArticle($GLOBALS['objPage'], 1);
					break;

				case 'inherit_all_articles':
					$content = $this->inheritArticle($GLOBALS['objPage']);
					break;

				case 'inherit_articles_fallback':
					$content = $this->getPageFrontendModule($GLOBALS['objPage'], 0, $this->strColumn);
					if (!strlen($content)) {
						$content = $this->inheritArticle($GLOBALS['objPage'], 1);
					}
					break;

				case 'inherit_all_articles_fallback':
					$content = $this->getPageFrontendModule($GLOBALS['objPage'], 0, $this->strColumn);
					if (!strlen($content)) {
						$content = $this->inheritArticle($GLOBALS['objPage']);
					}
					break;

				default:
					$content = $this->getPageFrontendModule($GLOBALS['objPage'], $module['content'], $this->strColumn);
				}

				$tpl->content .= $content;
				if ($result === null) {
					$result = strlen($content) > 0;
				}
			}
			if ($result && $this->isModeUpFirstTrue() || !$result && $this->isModeUpFirstFalse()) {
				break;
			}
		}

		return $tpl->parse();
	}

}
