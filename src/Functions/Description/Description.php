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
 * Class Description.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions\Description
 */
final class Description implements \JsonSerializable
{
    /**
     * Name of the function description.
     *
     * @var string
     */
    private $name;

    /**
     * Description of the function.
     *
     * @var string
     */
    private $description;

    /**
     * List of arguments.
     *
     * @var array
     */
    private $arguments = [];

    /**
     * Description constructor.
     *
     * @param string $name Name of the function description.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Create a description.
     *
     * @param string $name Name of the function description.
     *
     * @return static
     */
    public static function create($name)
    {
        return new static($name);
    }

    /**
     * Set the description.
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
     * Add an argument description.
     *
     * @param string $name Name of the argument.
     *
     * @return Argument
     */
    public function addArgument($name)
    {
        $argument          = new Argument($this, $name);
        $this->arguments[] = $argument;

        return $argument;
    }

    /**
     * Get arguments.
     *
     * @return Argument[]|array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Get description as array.
     *
     * @return array
     *
     * @deprecated Use jsonSerialize instead. Will be removed in 4.0.0 stable release.
     */
    public function toArray()
    {
        return $this->jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
            'arguments'   => array_map(
                function (Argument $argument) {
                    return $argument->toArray();
                },
                $this->arguments
            ),
        ];
    }
}
