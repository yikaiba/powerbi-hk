<?php

namespace Tngnt\PBI\Model;

/**
 * Class Column
 * @package Tngnt\PBI\Model
 */
class Column
{
    const INT64 = 'Int64';
    const DOUBLE = 'Double';
    const BOOL = 'Boolean';
    const DATETIME = 'Datetime';
    const STRING = 'String';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dataType;

    /**
     * Column constructor.
     * @param string $name The name of the column
     * @param string $dataType The data type of the column
     */
    public function __construct($name, $dataType)
    {
        $this->name = $name;
        $this->dataType = $dataType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }
}
