<?php
namespace TakaakiMizuno\MWBParser;

use TakaakiMizuno\MWBParser\Elements\Table;

class Parser
{
    /** @var string $path */
    protected $path;

    /** @var string $directory */
    protected $directory;

    /** @var \SimpleXMLElement $data */
    protected $data;

    /** @var \TakaakiMizuno\MWBParser\Elements\Table[] */
    protected $tables;

    public function __construct($path)
    {
        if ($this->extractArchive($path)) {
            $this->parse();
        }
    }

    /**
     * @return bool|string
     */
    protected function getTemporaryPath()
    {
        $tempDirectory = tempnam(sys_get_temp_dir(), 'mwb');
        @unlink($tempDirectory);
        @mkdir($tempDirectory);

        return $tempDirectory;
    }

    /**
     * @param string $path
     */
    protected function deleteDirectory($path)
    {
        if (is_dir($path)) {
            $objects = scandir($path);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($path.DIRECTORY_SEPARATOR.$object) == 'dir') {
                        $this->deleteDirectory($path.DIRECTORY_SEPARATOR.$object);
                    } else {
                        unlink($path.DIRECTORY_SEPARATOR.$object);
                    }
                }
            }
            reset($objects);
            rmdir($path);
        }
    }

    protected function extractArchive($file)
    {
        $this->path      = $file;
        $this->directory = $this->getTemporaryPath();

        if (!file_exists($file)) {
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($file) === true) {
            $zip->extractTo($this->directory);
            $zip->close();
        } else {
            return false;
        }

        return true;
    }

    protected function parse()
    {
        $xml        = file_get_contents($this->directory.DIRECTORY_SEPARATOR.'document.mwb.xml');
        $this->data = new \SimpleXMLElement($xml);
        $this->parseXML();
        $this->deleteDirectory($this->directory);
    }

    protected function parseXML()
    {
        $tables = $this->data->xpath('//value[@struct-name="db.mysql.Table"]');
        foreach ($tables as $table) {
            $this->tables[] = new Table($table);
        }
        $tableIds = [];
        foreach ($this->tables as $table) {
            $tableIds[$table->getId()] = $table;
        }
        foreach ($this->tables as $table) {
            $table->resolveForeignKeyReference($tableIds);
        }
    }

    /**
     * @return \TakaakiMizuno\MWBParser\Elements\Table[]
     */
    public function getTables()
    {
        return $this->tables;
    }
}
