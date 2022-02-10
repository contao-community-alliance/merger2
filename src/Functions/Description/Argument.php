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

namespace ContaoCommunityAlliance\Merger2\Functions\Description;

/**
 * Class Argument.
 *
 * @package ContaoCommunityAlliance\Merger2\Functions\Description
 */
final class Argument implements \JsonSerializable
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
     * @var int
     */
    private $type = self::TYPE_STRING;

    /**
     * Description of the argument.
     *
     * @var string
     */
    private $description = '';

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
    public function __construct(Description $parent, string $name)
    {
        $this->parent = $parent;
        $this->name   = $name;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
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
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param int $type Type.
     *
     * @return $this
     */
    public function setType(int $type): self
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
    public function setDefaultValue($value): self
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
    public function isOptional(): bool
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
    public function end(): Description
    {
        return $this->parent;
    }

    /**
     * Get argument description as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
            'optional'    => $this->optional,
            'default'     => $this->default,
            'type'        => $this->type,
        ];
    }
}
