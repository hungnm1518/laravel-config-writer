<?php

namespace HungNM\LCW\Config;

use Exception;
use HungNM\LCW\Config\DataWriter\FileWriter;
use Illuminate\Config\Repository as RepositoryBase;

class Repository extends RepositoryBase
{
    /**
     * The config rewriter object.
     *
     * @var HungNM\LCW\Config\FileWriter
     */
    protected $writer;

    /**
     * Create a new configuration repository.
     *
     * @param  HungNM\LCW\Config\FileWriter $writer
     * @param  array $items
     * @return void
     */
    public function __construct(FileWriter $writer, array $items = [])
    {
        parent::__construct($items);
        $this->writer = $writer;
    }

    /**
     * Write a given configuration value to file.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function write(string $key, $value): bool
    {
        $result = $this->writer->write($key, $value);

        if(!$result) throw new Exception('File could not be written to');

        $this->set($key, $value);

        return $result;
    }
}