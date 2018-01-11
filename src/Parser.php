<?php
namespace TakaakiMizuno\MWBParser;

use TakaakiMizuno\MWBParser\Elements\Table;

class Parser
{
    /** @var string $path */
    private $path;

    /** @var string $directory */
    private $directory;

    /** @var \SimpleXMLElement $data */
    private $data;

    /** @var \TakaakiMizuno\MWBParser\Elements\Table[] */
    private $tables;

    public function __construct($path)
    {
        if ($this->extractArchive($path)) {
            $this->parse();
        }
    }

    /**
     * @return bool|string
     */
    private function getTemporaryPath()
    {
        $tempDirectory = tempnam(sys_get_temp_dir(), 'mwb');
        @unlink($tempDirectory);
        @mkdir($tempDirectory);

        return $tempDirectory;
    }

    /**
     * @param string $path
     */
    private function deleteDirectory($path)
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

    private function extractArchive($file)
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

    private function parse()
    {
        $xml        = file_get_contents($this->directory.DIRECTORY_SEPARATOR.'document.mwb.xml');
        $this->data = new \SimpleXMLElement($xml);
        $this->parseXML();
        $this->deleteDirectory($this->directory);
    }

    private function parseXML()
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
