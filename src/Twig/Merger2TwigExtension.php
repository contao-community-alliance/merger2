<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2018 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0-or-later
 * @link      https://github.com/contao-community-alliance/merger2
 */

namespace ContaoCommunityAlliance\Merger2\Twig;

use ContaoCommunityAlliance\Merger2\Functions\Description\Argument;
use ContaoCommunityAlliance\Merger2\Functions\Description\Description;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class Merger2TwigExtension
 */
final class Merger2TwigExtension extends AbstractExtension
{
    /**
     * Argument type label reference.
     *
     * @var array
     */
    private $types;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ArgumentTypeExtension constructor.
     *
     * @param TranslatorInterface $translator Translator.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->types      = [
            Argument::TYPE_STRING  => 'string',
            Argument::TYPE_FLOAT   => 'float',
            Argument::TYPE_INTEGER => 'integer',
            Argument::TYPE_BOOLEAN => 'bool',
        ];
    }

    /**
     * Get the filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('merger2ArgumentType', [$this, 'argumentTypeFilter']),
            new TwigFilter('transMerger2Function', [$this, 'translateDescription']),
            new TwigFilter('transMerger2Argument', [$this, 'translateArgument']),
        ];
    }

    /**
     * The argument type filter translates the argument type into an readable format.
     *
     * @param int    $value     Given argument type value.
     * @param string $separator Separator of the different values.
     *
     * @return string
     */
    public function argumentTypeFilter(int $value, string $separator = '|'): string
    {
        $label = [];

        foreach ($this->types as $type => $typeLabel) {
            if (($value & $type) === $type) {
                $label[] = $typeLabel;
            }
        }

        return implode($label, $separator);
    }

    /**
     * Translate a function description.
     *
     * @param Description $description The function description.
     *
     * @return string
     */
    public function translateDescription(Description $description): string
    {
        $key = sprintf(
            'function.%s',
            $this->toLowerCase($description->getName())
        );

        return $this->translate($key, $description->getDescription());
    }

    /**
     * Translate an argument description.
     *
     * @param Argument $argument The argument description.
     *
     * @return string
     */
    public function translateArgument(Argument $argument): string
    {
        $key = sprintf(
            'function.%s.%s',
            $this->toLowerCase($argument->end()->getName()),
            $this->toLowerCase($argument->getName())
        );

        return $this->translate($key, $argument->getDescription());
    }

    /**
     * Convert a camel case value to a lower case.
     *
     * @param string $label The given label.
     *
     * @return string
     */
    private function toLowerCase(string $label): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $label));
    }

    /**
     * Translate a key. Return default if translated is the same as the key.
     *
     * @param string $key     The language key.
     * @param string $default The default value.
     *
     * @return string
     */
    private function translate(string $key, string $default): string
    {
        $translated = $this->translator->trans($key, [], 'merger2');

        if ($translated === $key) {
            return $default;
        }

        return $translated;
    }
}
