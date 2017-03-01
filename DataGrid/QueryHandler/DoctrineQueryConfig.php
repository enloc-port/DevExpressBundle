<?php

namespace Bilendi\DevExpressBundle\DataGrid\QueryHandler;

use Bilendi\DevExpressBundle\DataGrid\Expression\Visitable;

/**
 * Class DoctrineQueryConfig.
 */
class DoctrineQueryConfig
{
    /**
     * @var array
     */
    protected $fieldMapping = [];

    /**
     * @var Visitable[]
     */
    protected $defaultFilters = [];

    /**
     * @var bool
     */
    protected $caseSensitive = false;


    /**
     * DoctrineQueryConfig constructor.
     * @param array $fieldMapping
     * @param array $defaultFilters
     * @param bool $caseSensitive
     */
    public function __construct(array $fieldMapping = [], array $defaultFilters = [], $caseSensitive = false)
    {
        $this->fieldMapping = $fieldMapping;
        $this->defaultFilters = $defaultFilters;
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * @return Visitable[]
     */
    public function getDefaultFilters(): array
    {
        return $this->defaultFilters;
    }

    /**
     * @param array $defaultFilters
     */
    public function setDefaultFilters(array $defaultFilters)
    {
        $this->defaultFilters = $defaultFilters;
    }

    /**
     * @return array
     */
    public function getFieldMapping(): array
    {
        return $this->fieldMapping;
    }

    /**
     * @param array $fieldMapping
     */
    public function setFieldMapping(array $fieldMapping)
    {
        $this->fieldMapping = $fieldMapping;
    }

    /**
     * @param string $field
     *
     * @return string
     */
    public function mapField(string $field)
    {
        if (array_key_exists($field, $this->fieldMapping)) {
            return $this->fieldMapping[$field];
        }

        return $field;
    }

    /**
     * @return boolean
     */
    public function isCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    /**
     * @param boolean $caseSensitive
     */
    public function setCaseSensitive(bool $caseSensitive)
    {
        $this->caseSensitive = $caseSensitive;
    }
}
