<?php

namespace vaino78\CsvHelper;

class Reader extends AbstractHandler
{
    const OPTION_FIRST_LINE_HEADER = 1;

    const OPTION_FETCH_NO_ASSOC = 2;

    /**
     * @var source
     */
    protected $fileHandle;

    /**
     * @var int
     */
    protected $length;

    /**
     * @param source $fh
     * @param int $length
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($fh, $length = 0, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $this->fileHandle = $fh;

        $this->setLength($length)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);
    }

    /**
     * @return int
     */
    public function getLenght()
    {
        return $this->length;
    }

    /**
     * @return \Generator
     */
    public function read()
    {
        fseek($this->fileHandle, 0);

        if($this->hasOption(static::OPTION_FIRST_LINE_HEADER)) {
            $cols = $this->getCsv();
            if(is_array($cols)) {
                $this->setColumns($cols);
            }
        }

        while(($data = $this->getCsv()) !== false) {
            yield $this->getOutput($data);
        }
    }

    /**
     * @param int $length
     * @return \Mts\Import\CsvReader
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return mixed
     * @uses fgetcsv()
     */
    protected function getCsv()
    {
        return fgetcsv(
            $this->fileHandle,
            $this->length,
            $this->delimiter,
            $this->enclosure,
            $this->escape
        );
    }

    /**
     * @param mixed $data
     * @return mixed
     *
     * @uses optimizeDataLenght()
     */
    protected function getOutput($data)
    {
        if(!$data || !is_array($data)) {
            return $data;
        }

        if($this->hasOption(static::OPTION_FETCH_NO_ASSOC) || empty($this->columns)) {
            return $data;
        }

        $data = $this->optimizeDataLenght($data, count($this->columns));

        return array_combine($this->columns, $data);
    }

    /**
     * @param array $data
     * @param int $length
     * @return array
     */
    protected function optimizeDataLenght(array $data, $length)
    {
        $dataLength = count($data);

        if($dataLength > $length) {
            return array_slice($data, 0, $length);
        } elseif($dataLength < $length) {
            return array_merge(
                $data,
                array_fill(0, ($length - $dataLength), null),
                null
            );
        }

        return $data;
    }

    function __destruct()
    {
        $this->fileHandle = null;
    }
}
