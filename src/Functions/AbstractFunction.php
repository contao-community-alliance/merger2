<?php

/**
 * Merger² - Module Merger for Contao Open Source CMS.
 *
 * @package   Merger²
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @copyright 2013-2014 bit3 UG
 * @copyright 2015-2017 Contao Community Alliance
 * @license   https://github.com/contao-community-alliance/merger2/blob/master/LICENSE LGPL-3.0+
 * @link      https://github.com/contao-community-alliance/merger2
 */

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
     * @var string
     */
    protected static $names = [];

    /**
     * Get name.
     *
     * @return string
     */
    public static function getName()
    {
        $class = get_called_class();

        if (!isset(static::$names[$class])) {
            $className             = array_pop(explode('\\', $class));
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
