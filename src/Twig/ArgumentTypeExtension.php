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

namespace ContaoCommunityAlliance\Merger2\Twig;

use ContaoCommunityAlliance\Merger2\Functions\Description\Argument;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class ArgumentTypeExtension
 *
 * @package ContaoCommunityAlliance\Merger2\Twig
 */
final class ArgumentTypeExtension extends AbstractExtension
{
    /**
     * Argument type label reference.
     *
     * @var array
     */
    private $types;

    /**
     * ArgumentTypeExtension constructor.
     */
    public function __construct()
    {
        $this->types = [
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
}
