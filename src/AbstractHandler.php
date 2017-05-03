<?php

namespace vaino78\CsvHelper;

abstract class AbstractHandler 
{
    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $enclosure;

    /**
     * @var string
     */
    protected $escape;

    /**
     * @var int
     */
    protected $options = 0;

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * @param int $option
     * @return bool
     */
    public function hasOption($option)
    {
        return !!($this->options & $option);
    }

    /**
     * @param array $columns
     * @return self
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param string $delimiter
     * @return self
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @param string $enclosure
     * @return self
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * @param string $escape
     * @return self
     */
    public function setEscape($escape)
    {
        $this->escape = $escape;

        return $this;
    }


    /**
     * @param int $option
     * @param bool $enabled
     *
     * @return self
     */
    public function setOptionEnabled($option, $enabled)
    {
        $this->options = ($enabled)
            ? ($this->options | $option)
            : ($this->options & ~$option);

        return $this;
    }
}
