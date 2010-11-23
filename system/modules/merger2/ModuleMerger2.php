<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2009-2010 Leo Feyer
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
 * @copyright  2009-2010, InfinityLabs 
 * @author     Tristan Lins <tristan.lins@infinitylabs.de>
 * @package    Merger2
 * @license    LGPL 
 * @filesource
 */


/**
 * Class ModuleMerger2
 *
 * @copyright  2009, InfinityLabs 
 * @author     Tristan Lins <tristan.lins@infinitylabs.de>
 * @package    ModuleMerger
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
	 * 
	 * @param mixed $language
	 * @return boolean
	 */
	private function language($language) {
		return ($GLOBALS['TL_LANGUAGE'] == strtolower($language));
	}
	
	/**
	 * function: page(..)
	 * 
	 * @param mixed $id
	 * @return boolean
	 */
	private function page($id) {
		global $objPage;
		return (intval($id) == $objPage->id || $id == $objPage->alias) ? true : false;
	}
	
	/**
	 * function: pageInPath(..)
	 * 
	 * @param mixed $id
	 * @return boolean
	 */
	private function pageInPath($id) {
		$page = $GLOBALS['objPage'];
		while (true) {
			if (intval($id) == $page->id || $id == $page->alias)
				return true;
			if ($page->pid > 0) {
				$page = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
									   ->limit(1)
									   ->execute($page->pid);
				if (!$page->next())
					return false;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Boolean evaluate function.
	 * 
	 * @param string $value
	 * @return boolean
	 */
	private function boolean($value) {
		switch (strtolower($value)) {
		case 'true': case '1': case '!false': case '!0': return true;
		case 'false': case '0': case '!true': case '!1': return false;
		default: throw new Exception('Illegal boolean value: "' . $value . '"');
		}
	}
	
	/**
	 * function: depth(..)
	 * 
	 * @param mixed $value
	 * @return boolean
	 */
	private function depth($value) {
		if (preg_match('#^(<|>|<=|>=|=|!=)?\\s*(\\d+)$#', $value, $m)) {
			$cmp = $m[1] ? $m[1] : '=';
			$i = intval($m[2]);
			
			$n = 0;
			$page = $GLOBALS['objPage'];
			while ($page->pid > 0 && $page->type != 'root') {
				$n ++;
				$page = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
									   ->limit(1)
									   ->execute($page->pid);
				if (!$page->next())
					break;
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
			throw new Exception('Illegal depth value: "' . $value . '"');
		}
	}
	
	/**
	 * function: articleExists(..)
	 * 
	 * @param string $column
	 * @param boolean $includeUnpublished
	 * @return boolean;
	 */
	function articleExists($column, $includeUnpublished = false) {
		global $objPage;
		$time = time();
		$objArticle = $this->Database->prepare("SELECT COUNT(id) as count FROM tl_article WHERE pid=? AND inColumn=?" .
										($includeUnpublished ? '' : " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1"))
									 ->limit(1)
									 ->execute($objPage->id, $column, $time, $time);
		if ($objArticle->next())
			return $objArticle->count > 0;
		else
			return false;
	}
	
	/**
	 * function: children(..)
	 * 
	 * @param integer $count
	 * @param boolean $includeUnpublished
	 * @return boolean
	 */
	function children($count, $includeUnpublished = false) {
		global $objPage;
		$time = time();
		$objChildren = $this->Database->prepare("SELECT COUNT(id) as count FROM tl_page WHERE pid=?" .
										($includeUnpublished ? '' : " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1"))
									 ->limit(1)
									 ->execute($objPage->id, $time, $time);
		if ($objChildren->next())
			return $objChildren->count >= $count;
		return false;
	}
	
	/**
	 * Evaluate a function with boolean result.
	 * 
	 * @param array $matches
	 * @return boolean
	 */
	public function evaluateFunctionBool($matches) {
		$args = explode(',', $matches[2]);
		switch ($matches[1]) {
			case 'language': return $this->language(trim($args[0]));
			case 'page': return $this->page(trim($args[0]));
			case 'pageInPath': return $this->pageInPath(trim($args[0]));
			case 'depth': return $this->depth(trim($args[0]));
			case 'articleExists': return $this->articleExists(isset($args[0]) ? trim($args[0]) : 'main');
			case 'articleExistsReal': return $this->articleExists(isset($args[0]) ? trim($args[0]) : 'main', true);
			case 'children': return $this->children(isset($args[0]) && is_numeric($args[0]) ? trim($args[0]) : 1);
			case 'childrenReal': return $this->children(isset($args[0]) && is_numeric($args[0]) ? trim($args[0]) : 1, true);
			default: throw new Exception('Illegal function: ' . trim($matches[0]));
		}
	}
	
	/**
	 * Evaluate a function with string result.
	 * 
	 * @param array $matches
	 * @return string
	 */
	public function evaluateFunction($matches) {
		return $this->evaluateFunctionBool($matches) ? 'true' : 'false';
	}
	
	/**
	 * Evaluate AND concatenation.
	 * 
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
	 * 
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
	 * 
	 * @param array $matches
	 * @return string
	 */
	public function evaluateBlock($matches) {
		return $this->evaluate(trim($matches[1])) ? 'true' : 'false';
	}
	
	/**
	 * Evaluate an expression.
	 * 
	 * @param string $expression
	 * @return boolean
	 */
	private function evaluate($expression) {
		$count = 0;
		do {
			$expression = preg_replace_callback('#(\\w+)\\(([^\\)]*)\\)#',
				array(&$this, 'evaluateFunction'),
				$expression,
				-1,
				$count);
		} while($count > 0);
		do {
			$expression = preg_replace_callback('#\\(([^\\)]+)\\)#', array(&$this, 'evaluateBlock'), $expression, -1, $count);
		} while ($count > 0);
		do {
			$expression = preg_replace_callback('#(!?true|!?false|!?0|!?1)\\s+(and|&)\\s+(!?true|!?false|!?0|!?1)#i',
				array(&$this, 'evaluateAnd'),
				$expression,
				-1,
				$count);
		} while($count > 0);
		do {
			$expression = preg_replace_callback('#(!?true|!?false|!?0|!?1)\\s+(or|\\|)\\s+(!?true|!?false|!?0|!?1)#i',
				array(&$this, 'evaluateOr'),
				$expression,
				-1,
				$count);
		} while($count > 0);
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

			if (($count = $objArticles->numRows) < 1)
			{
				return '';
			}

			$return = '';

			while ($objArticles->next())
			{
				$return .= $this->getPageArticle($objPage, $objArticles->id, (($count > 1) ? true : false), false, $strColumn);
			}

			return $return;
		}

		// Other modules
		$objModule = $this->Database->prepare("SELECT * FROM tl_module WHERE id=?")
									->limit(1)
									->execute($intId);

		if ($objModule->numRows < 1)
		{
			return '';
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
			$this->log('Module class "'.$GLOBALS['FE_MOD'][$objModule->type].'" (module "'.$objModule->type.'") does not exist', 'Controller getFrontendModule()', TL_ERROR);
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
	 * @param integer
	 * @param boolean
	 * @param boolean
	 * @param string
	 * @return string
	 */
	protected function getPageArticle(&$objPage, $varId, $blnMultiMode=false, $blnIsInsertTag=false, $strColumn='main')
	{
		if (!$varId)
		{
			return '';
		}

		$this->import('Database');

		// Get article
		$objArticle = $this->Database->prepare("SELECT *, author AS authorId, (SELECT name FROM tl_user WHERE id=author) AS author FROM tl_article WHERE (id=? OR alias=?)" . (!$blnIsInsertTag ? " AND pid=?" : ""))
									 ->limit(1)
									 ->execute((is_numeric($varId) ? $varId : 0), $varId, $objPage->id);

		if ($objArticle->numRows < 1)
		{
			return '';
		}

		if (!file_exists(TL_ROOT . '/system/modules/frontend/ModuleArticle.php'))
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
		$objArticle->multiMode = $blnMultiMode;

		$objArticle = new ModuleArticle($objArticle, $strColumn);
		return $objArticle->generate($blnIsInsertTag);
	}

	protected function inheritArticle(&$objPage, $max = 0, $lvl = 0) {
		$objParent = $this->Database->prepare('SELECT * FROM tl_page WHERE id = ?')
									->limit(1)
									->execute($objPage->pid);
		if ($objParent->next()) {
			$html = $this->getPageFrontendModule($objParent, 0, $this->strColumn, true);
			if ($max == 0 || $max < ++$lvl)
				$html .= $this->inheritArticle($objParent, $max, $lvl);
			return $html;
		}
		return '';
	}
	
	protected function isModeAll() {
		return $this->mergerMode == 'all';
	}
	
	protected function isModeUpFirstFalse() {
		return $this->mergerMode == 'upFirstFalse';
	}
	
	protected function isModeUpFirstTrue() {
		return $this->mergerMode == 'upFirstTrue';
	}
	
	public function generate() {
		if (strlen($this->mergerContainer)) {
			return parent::generate();
		} else {
			return $this->generateContent();
		}
	}
	
	protected function compile()
	{
		$this->Template->content = $this->generateContent();
	}
	
	protected function generateContent() {
		$tpl = new FrontendTemplate($this->mergerTemplate);
		
		$modules = deserialize($this->mergerData);
		$tpl->content = '';
		foreach ($modules as $module) {
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
					$content = $this->getPageFrontendModule($GLOBALS['objPage'], $module, $this->strColumn);
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

?>