<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    Tristan Lins <tristan.lins@bit3.de>
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @author    Stefan Schulz-Lauterbach <ssl@clickpress.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2022 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\Module;

use Contao\ArticleModel;
use Contao\BackendTemplate;
use Contao\FrontendTemplate;
use Contao\Module;
use Contao\PageModel;
use Contao\StringUtil;
use ContaoCommunityAlliance\Merger2\Constraint\Parser\InputStream;
use ContaoCommunityAlliance\Merger2\Renderer\PageModuleRenderer;

/**
 * Class ModuleMerger2.
 */
final class ModuleMerger2 extends Module
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_merger2';

    /**
     * Page module renderer.
     *
     * @var PageModuleRenderer
     */
    private $pageModuleRenderer;

    /**
     * Generate a front end module and return it as HTML string.
     *
     * @param PageModel $page            Page model.
     * @param string    $moduleId        Frontend module id.
     * @param string    $columnName      Column or section name.
     * @param bool      $inheritableOnly If true only inheritable module is found.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getPageFrontendModule($page, $moduleId, $columnName = 'main', $inheritableOnly = false)
    {
        if (!$this->pageModuleRenderer) {
            $this->pageModuleRenderer = new PageModuleRenderer();
        }

        return $this->pageModuleRenderer->render($page, $moduleId, $columnName, $inheritableOnly);
    }

    /**
     * Generate an article and return it as string.
     *
     * @param PageModel $page      Page model.
     * @param int       $articleId Article id.
     *
     * @return string
     */
    protected function getPageArticle($page, $articleId)
    {
        $article = ArticleModel::findByIdOrAliasAndPid($articleId, $page->id);

        if ($article === null) {
            return '';
        }

        return $this->getArticle($article);
    }

    /**
     * Inherit article from parent page.
     *
     * @param PageModel $page         Page model.
     * @param int       $maxLevel     Max level.
     * @param int       $currentLevel Current level.
     *
     * @return string
     */
    protected function inheritArticle($page, $maxLevel = 0, $currentLevel = 0)
    {
        $parentPage = PageModel::findPublishedById($page->pid);

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
     * Mode is "all".
     *
     * @return bool
     */
    protected function isModeAll()
    {
        return $this->merger_mode == 'all';
    }

    /**
     * Mode is "up first false".
     *
     * @return bool
     */
    protected function isModeUpFirstFalse()
    {
        return $this->merger_mode == 'upFirstFalse';
    }

    /**
     * Mode is "up first true".
     *
     * @return bool
     */
    protected function isModeUpFirstTrue()
    {
        return $this->merger_mode == 'upFirstTrue';
    }

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

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
        } else {
            // or only the content
            return $this->generateContent();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function compile()
    {
        $this->Template->content = $this->generateContent();
    }

    /**
     * Generate content.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     *
     * @return string
     */
    protected function generateContent()
    {
        $modules = StringUtil::deserialize($this->merger_data);
        $buffer  = '';

        foreach ($modules as $module) {
            if ($module['disabled']) {
                continue;
            }

            $result = $this->evaluateCondition($module);

            if ($result || $result === null) {
                $content = $this->generateModuleContent($module);
                $buffer .= $content;

                if ($result === null) {
                    $result = strlen($content) > 0;
                }
            }

            if ($this->validateModeUpCondition($result)) {
                break;
            }
        }

        $tpl          = new FrontendTemplate($this->merger_template);
        $tpl->content = $buffer;

        return $tpl->parse();
    }

    /**
     * Evaluate the condition.
     *
     * @param array $module Module configuration.
     *
     * @return mixed
     */
    protected function evaluateCondition($module)
    {
        $result    = null;
        $condition = trim(html_entity_decode($module['condition']));

        if (strlen($condition)) {
            $input  = new InputStream($condition);
            $node   = $this->getContainer()->get('cca.merger2.constraint_parser')->parse($input);
            $result = $node->evaluate();
        }

        return $result;
    }

    /**
     * Generate module content.
     *
     * @param array $module Module configuration.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function generateModuleContent($module)
    {
        $content = '';
        switch ($module['content']) {
            case '-':
                break;

            /*
             * Include the articles from current page.
             */

            case 'article':
                $content = $this->getPageFrontendModule($GLOBALS['objPage'], 0, $this->strColumn);
                break;

            /*
             * Inherit articles from one upper level that contains articles.
             */

            case 'inherit_articles':
                $content = $this->inheritArticle($GLOBALS['objPage'], 1);
                break;

            /*
             * Inherit articles from all upper levels.
             */

            case 'inherit_all_articles':
                $content = $this->inheritArticle($GLOBALS['objPage']);
                break;

            /*
             * Include the articles from current page or inherit from one upper level that contains articles.
             */

            case 'inherit_articles_fallback':
                $content = $this->getPageFrontendModule($GLOBALS['objPage'], 0, $this->strColumn);

                if (!strlen($content)) {
                    $content = $this->inheritArticle($GLOBALS['objPage'], 1);
                }
                break;

            /*
             * Include the articles from current page or inherit from upper all upper levels.
             */

            case 'inherit_all_articles_fallback':
                $content = $this->getPageFrontendModule($GLOBALS['objPage'], 0, $this->strColumn);

                if (!strlen($content)) {
                    $content = $this->inheritArticle($GLOBALS['objPage']);
                }
                break;

            /*
             * Include a module.
             */

            default:
                $content = $this->getPageFrontendModule(
                    $GLOBALS['objPage'],
                    $module['content'],
                    $this->strColumn
                );
        }

        return $content;
    }

    /**
     * Validate the mode up condition for the given result.
     *
     * @param string $result Result of module generation.
     *
     * @return bool
     */
    private function validateModeUpCondition($result)
    {
        return $result && $this->isModeUpFirstTrue() || !$result && $this->isModeUpFirstFalse();
    }
}
