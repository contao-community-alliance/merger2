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

namespace ContaoCommunityAlliance\Merger2\Util;

/**
 * Class CompareUtil
 */
final class CompareUtil
{
    /**
     * Compare a value with an expected value.
     *
     * @param mixed       $value      THe given value.
     * @param mixed       $expected   Expected value to compare with.
     * @param null|string $comparator The comparision operator.
     *
     * @return bool
     */
    public static function compare($value, $expected, ?string $comparator = null): bool
    {
        $comparator = $comparator ?: '=';

        switch ($comparator) {
            case '<':
                return $value < $expected;
            case '>':
                return $value > $expected;
            case '<=':
                return $value <= $expected;
            case '>=':
                return $value >= $expected;
            case '!=':
            case '<>':
                return $value != $expected;
            default:
                return $value == $expected;
        }
    }
}
