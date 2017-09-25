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

namespace ContaoCommunityAlliance\Merger2\Functions\Description;

/**
 * Class Argument.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions\Description
 */
class Argument
{
    const TYPE_STRING  = 1;
    const TYPE_FLOAT   = 2;
    const TYPE_INTEGER = 4;
    const TYPE_BOOLEAN = 8;
    /**
     * Parent function description.
     *
     * @var Description
     */
    private $parent;

    /**
     * Name of the arguments.
     *
     * @var string
     */
    private $name;

    /**
     * Type of the argument.
     *
     * @var string
     */
    private $type = self::TYPE_STRING;

    /**
     * Description of the argument.
     *
     * @var string
     */
    private $description;

    /**
     * Optional flag.
     *
     * @var bool
     */
    private $optional = false;

    /**
     * Default value.
     *
     * @var null
     */
    private $default = null;

    /**
     * Argument constructor.
     *
     * @param Description $parent Parent function description.
     * @param string      $name   Argument name.
     */
    public function __construct(Description $parent, $name)
    {
        $this->parent = $parent;
        $this->name   = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description Description.
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string $type Type.
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the default value.
     *
     * @param mixed $value Default value.
     *
     * @return $this
     */
    public function setDefaultValue($value)
    {
        $this->default  = $value;
        $this->optional = true;

        return $this;
    }

    /**
     * Get optional.
     *
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * Get default.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * End argument building.
     *
     * @return Description
     */
    public function end()
    {
        return $this->parent;
    }
}
