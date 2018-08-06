<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2018 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

declare(strict_types=1);

namespace ContaoCommunityAlliance\Merger2\EventListener\Hook;

use ContaoCommunityAlliance\Merger2\Functions\Description\Argument;
use ContaoCommunityAlliance\Merger2\Functions\FunctionCollection;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class LoadFunctionExplanationsListener generated the functions explanations at runtime.
 *
 * All functions of Merger² describe themselves. This content is rendered dynamically during loadLanguageFile hook.
 */
final class LoadFunctionExplanationsListener
{
    /**
     * The function collection.
     *
     * @var FunctionCollection
     */
    private $functions;

    /**
     * The templating engine.
     *
     * @var EngineInterface
     */
    private $templating;

    /**
     * LoadFunctionExplanationsListener constructor.
     *
     * @param FunctionCollection $functions  The function collection.
     * @param EngineInterface    $templating The templating engine.
     */
    public function __construct(
        FunctionCollection $functions,
        EngineInterface $templating
    ) {
        $this->functions  = $functions;
        $this->templating = $templating;
    }

    /**
     * Handle the loadLanguage file hook.
     *
     * @param string $name     The language domain.
     * @param string $language The language.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function onLoadLanguageFile(string $name, string $language): void
    {
        if ($name !== 'explain' || isset($GLOBALS['TL_LANG']['XPL']['merger2Functions'])) {
            return;
        }

        $GLOBALS['TL_LANG']['XPL']['merger2Functions'] = $this->renderExplanation($language);
    }

    /**
     * Render the explanation.
     *
     * @param string $language The requested language.
     *
     * @return string
     */
    private function renderExplanation(string $language): string
    {
        $descriptions = $this->functions->getDescriptions();
        $explanation  = $this->templating->render(
            '@CcaMerger2/explanation.html5.twig',
            [
                'descriptions' => $descriptions,
                'locale'       => $language,
            ]
        );

        return $explanation;
    }
}
