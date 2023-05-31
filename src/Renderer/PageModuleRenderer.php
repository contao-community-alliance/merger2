<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2022 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\Renderer;

use Contao\ArticleModel;
use Contao\Controller;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Input;
use Contao\Model\Collection;
use Contao\Module;
use Contao\ModuleModel;
use Contao\PageModel;
use Psr\Log\LoggerInterface;

use function array_pad;
use function is_string;

/**
 * Class PageModuleRenderer.
 *
 * @package ContaoCommunityAlliance\Merger2\Renderer
 */
final class PageModuleRenderer
{
    /**
     * Optional logger.
     *
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * Construct.
     *
     * @param LoggerInterface|null $logger Optional logger.
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Generate a front end module and return it as HTML string.
     *
     * @param PageModel              $page            Page model.
     * @param int|string|ModuleModel $moduleId        Frontend module id.
     * @param string                 $columnName      Column or section name.
     * @param bool                   $inheritableOnly If true only inheritable module is found.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function render($page, $moduleId, $columnName = 'main', $inheritableOnly = false)
    {
        if (!is_object($moduleId) && (is_string($moduleId) && !strlen($moduleId))) {
            return '';
        }

        // Articles
        if ($moduleId == 0) {
            return $this->renderArticles($page, $columnName, $inheritableOnly);
        }

        return $this->renderModule($moduleId, $columnName);
    }

    /**
     * Render a particular article.
     *
     * @param PageModel $page            Page model.
     * @param string    $columnName      Column or section name.
     * @param bool      $inheritableOnly Only recognize inheritable articles.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function renderArticles($page, $columnName, $inheritableOnly)
    {
        // Show a particular article only
        if ($page->type === 'regular' && Input::get('articles')) {
            $article = $this->renderColumnArticle($page, $columnName, $inheritableOnly);

            if (is_string($article)) {
                return $article;
            }
        }

        $buffer = $this->callGetArticlesHook($page, $columnName);
        if (is_string($buffer)) {
            return $buffer;
        }

        // Show all articles (no else block here, see #4740)
        $articleCollection = ArticleModel::findPublishedByPidAndColumn($page->id, $columnName);

        if (!$articleCollection instanceof Collection) {
            return '';
        }

        $return    = '';
        $count     = 0;
        $multiMode = ($articleCollection->count() > 1);
        $last      = ($articleCollection->count() - 1);

        foreach ($articleCollection as $articleModel) {
            if ($inheritableOnly && !$articleModel->inheritable) {
                continue;
            }

            $return .= $this->renderArticle($columnName, $articleModel->current(), $count, $last, $multiMode);
            ++$count;
        }

        return $return;
    }

    /**
     * Render an article if article is in the column.
     *
     * If article is not in the defined section false returned.
     *
     * @param PageModel $page            Page model.
     * @param string    $columnName      Column name or section.
     * @param bool      $inheritableOnly Only recognize inheritable articles.
     *
     * @return bool|string
     */
    private function renderColumnArticle($page, $columnName, $inheritableOnly)
    {
        /** @psalm-suppress PossiblyInvalidCast */
        [$sectionName, $articleName] = array_pad(explode(':', (string) Input::get('articles')), 2, null);

        if ($articleName === null) {
            $articleName = $sectionName;
            $sectionName = 'main';
        }

        if ($sectionName === $columnName) {
            $article = ArticleModel::findByIdOrAliasAndPid($articleName, $page->id);

            if ($article === null) {
                return false;
            }

            $this->guardArticleExists($article, $articleName);
            $this->guardArticleIsVisible($article, $articleName);


            if (!$inheritableOnly || $article->inheritable) {
                // Add the "first" and "last" classes (see #2583)
                $article->classes = array('first', 'last');

                return Controller::getArticle($article);
            }

            return '';
        }

        return false;
    }

    /**
     * Guard that an article exists or throw PageNotFoundException.
     *
     * @param ArticleModel|null $article     Article.
     * @param mixed             $articleName Article name or id.
     *
     * @return void
     * @throws PageNotFoundException When article not exists.
     */
    private function guardArticleExists($article, $articleName)
    {
        // Send a 404 header if the article does not exist
        if ($article === null) {
            throw new PageNotFoundException('Page not found: ' . $articleName);
        }
    }

    /**
     * Guard that article is visible or throw AccessDeniedException.
     *
     * @param ArticleModel|null $article     Article.
     * @param mixed             $articleName Article name or id.
     *
     * @return void
     * @throws AccessDeniedException When article is not visible.
     */
    private function guardArticleIsVisible($article, $articleName)
    {
        // Send a 403 header if the article cannot be accessed
        if ($article && !Controller::isVisibleElement($article)) {
            throw new AccessDeniedException('Access denied: ' . $articleName);
        }
    }

    /**
     * Call the getArticles hook.
     *
     * @param PageModel $page       Page model.
     * @param string    $columnName Column or section name.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function callGetArticlesHook($page, $columnName)
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['getArticles']) && is_array($GLOBALS['TL_HOOKS']['getArticles'])) {
            foreach ($GLOBALS['TL_HOOKS']['getArticles'] as $callback) {
                $return = Controller::importStatic($callback[0])->{$callback[1]}($page->id, $columnName);

                if (is_string($return)) {
                    return $return;
                }
            }
        }

        return false;
    }

    /**
     * Render a single article and add classes.
     *
     * @param string       $columnName Column or section name.
     * @param ArticleModel $article    Article model.
     * @param int          $count      Current position of the article.
     * @param int          $last       Position of the last article.
     * @param bool         $multiMode  If true, only teasers will be shown.
     *
     * @return string|bool
     */
    private function renderArticle($columnName, $article, $count, $last, $multiMode)
    {
        // Add the "first" and "last" classes (see #2583)
        if ($count == 0 || $count == $last) {
            $cssClasses = array();

            if ($count == 0) {
                $cssClasses[] = 'first';
            }

            if ($count == $last) {
                $cssClasses[] = 'last';
            }

            $article->classes = $cssClasses;
        }

        return Controller::getArticle($article, $multiMode, false, $columnName);
    }

    /**
     * Render a frontend module.
     *
     * @param int|string|ModuleModel $moduleId   Frontend module id or model.
     * @param string                 $columnName Section or column name.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function renderModule($moduleId, $columnName)
    {
        // Other modules
        if (is_object($moduleId)) {
            $articleRow = $moduleId;
        } else {
            $articleRow = ModuleModel::findByPk($moduleId);

            if ($articleRow === null) {
                return '';
            }
        }

        // Check the visibility (see #6311)
        if (!Controller::isVisibleElement($articleRow)) {
            return '';
        }

        $moduleClassName = Module::findClass($articleRow->type);

        // Return if the class does not exist
        if (!class_exists($moduleClassName)) {
            if (! $this->logger) {
                return '';
            }

            $this->logger->error(
                'Module class "' . $moduleClassName . '" (module "' . $articleRow->type . '") does not exist',
            );

            return '';
        }

        $articleRow->typePrefix = 'mod_';
        /** @var Module $module */
        $module = new $moduleClassName($articleRow, $columnName);
        $buffer = $module->generate();
        $buffer = $this->callGetFrontendModuleHook($articleRow, $buffer, $module);


        // Disable indexing if protected
        if ($module->protected && !preg_match('/^\s*<!-- indexer::stop/', $buffer)) {
            $buffer = "\n<!-- indexer::stop -->" . $buffer . "<!-- indexer::continue -->\n";
        }

        return $buffer;
    }

    /**
     * Call the get frontend module hook.
     *
     * @param ModuleModel $moduleModel Module model.
     * @param string      $buffer      Generated article.
     * @param Module      $module      Module class.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function callGetFrontendModuleHook($moduleModel, $buffer, $module)
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['getFrontendModule'])
            && is_array($GLOBALS['TL_HOOKS']['getFrontendModule'])
        ) {
            foreach ($GLOBALS['TL_HOOKS']['getFrontendModule'] as $callback) {
                $buffer = Controller::importStatic($callback[0])->{$callback[1]}($moduleModel, $buffer, $module);
            }
        }

        return $buffer;
    }
}
