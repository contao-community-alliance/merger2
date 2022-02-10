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

namespace ContaoCommunityAlliance\Merger2\Functions;

/**
 * Class AbstractFunction.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions
 */
abstract class AbstractFunction implements FunctionInterface
{
    /**
     * Name of the function.
     *
     * @var array<class-string,string>
     */
    protected static $names = [];

    /**
     * Get name.
     *
     * @return string
     */
    public static function getName(): string
    {
        $class = get_called_class();

        if (!isset(static::$names[$class])) {
            $parts                 = explode('\\', $class);
            $className             = array_pop($parts);
            static::$names[$class] = lcfirst(substr($className, 0, -8));
        }

        return static::$names[$class];
    }
    /**
     * {@inheritDoc}
     */
    public function invoke(array $arguments = [])
    {
        return call_user_func_array([$this, '__invoke'], $arguments);
    }
}
