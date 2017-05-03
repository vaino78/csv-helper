<?php

namespace vaino78\CsvHelper;

class Writer extends AbstractHandler
{
    const OPTION_FIRST_LINE_HEADER = 1;

    const OPTION_GET_HEADER_BY_FIRST_LINE = 2;

    /**
     * @var source
     */
    protected $fileHandle;

    /**
     * @var bool
     */
    protected $isHeaderFlushed = false;

    /**
     * @var int Lines count
     */
    protected $lines = 0;

    /**
     * @param source $fileHandle
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($fileHandle, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $this->fileHandle = $fileHandle;

        $this->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);
    }

    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @return bool
     */
    public function isHeaderWrited()
    {
        return $this->isHeaderFlushed;
    }

    /**
     * @param array $data
     * @return int|false
     *
     * @uses putCsv()
     * @uses prepareData()
     * @uses writeHeader() If option `OPTION_FIRST_LINE_HEADER` is set
     */
    public function write(array $data)
    {
        if(
            !$this->lines
            && !$this->isHeaderWrited()
            && !$this->getColumns()
            && $this->isArrayAssoc($data)
            && $this->hasOption(static::OPTION_GET_HEADER_BY_FIRST_LINE)
        ) {
            $this->setColumns(array_keys($data));
        }

        if($this->hasOption(static::OPTION_FIRST_LINE_HEADER) && !$this->isHeaderWrited()) {
            $this->writeHeader();
        }

        $this->lines++;

        return $this->putCsv($this->prepareData($data));
    }

    /**
     * @return int|false
     */
    public function writeHeader()
    {
        $columns = $this->getColumns();

        if(empty($columns)) {
            trigger_error('Can not write headers because of empty column data', E_USER_WARNING);
            return false;
        }

        if($this->isHeaderFlushed) {
            trigger_error('Header is writed more than once', E_USER_WARNING);
        }

        $this->isHeaderFlushed = true;

        return $this->putCsv($columns);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $columns = $this->getColumns();

        if(empty($columns)) {
            return $data;
        }

        if(!$this->isArrayAssoc($data)) {
            return $data;
        }

        $result = array();
        foreach($columns as $column) {
            $result[] = (
                (isset($data[$column]))
                ? $data[$column]
                : ''
            );
        }

        return $result;
    }

    /**
     * @param array $arr
     * @return bool
     */
    protected function isArrayAssoc(array $arr)
    {
        $k = array_keys($arr);
        return ($k !== array_keys($k));
    }

    /**
     * @param array $data
     * @return int|false
     *
     * @uses fputcsv()
     */
    protected function putCsv(array $data)
    {
        return fputcsv(
            $this->fileHandle,
            $data,
            $this->delimiter,
            $this->enclosure,
            $this->escape
        );
    }

    function __destruct()
    {
        $this->fileHandle = null;
    }
}
