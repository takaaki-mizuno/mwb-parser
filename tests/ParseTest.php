<?php
namespace TakaakiMizuno\MWBParser\Tests;

use SebastianBergmann\CodeCoverage\Report\PHP;
use TakaakiMizuno\MWBParser\Parser;

class ParserTest extends Base
{
    public function testParseClass()
    {
        $file   =  implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'data', 'db.mwb']);
        $parser = new Parser($file);
        $tables = $parser->getTables();

        $this->assertEquals(3, count($tables));

        print PHP_EOL;
        foreach ($tables as $table) {
            print '>'.$table->getName().PHP_EOL;
            foreach ($table->getColumns() as $column) {
                print ' >>>'.$column->getName().' '.$column->getType().PHP_EOL;
            }
        }
    }
}
