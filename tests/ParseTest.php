<?php
namespace TakaakiMizuno\MWBParser\Tests;

use TakaakiMizuno\MWBParser\Parser;

class ParserTest extends Base
{
    public function testParseClass()
    {
        $file   =  implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'data', 'db.mwb']);
        $parser = new Parser($file);
        $tables = $parser->getTables();

        $this->assertEquals(4, count($tables));

        foreach ($tables as $table) {
            $this->assertNotEmpty($table->getName());
        }

        print PHP_EOL;
        foreach ($tables as $table) {
            print '>'.$table->getName().PHP_EOL;
            foreach ($table->getColumns() as $column) {
                print ' COLUMN >>>'.$column->getName().' '.$column->isUnsigned().' '.$column->getType().PHP_EOL;
            }
            foreach ($table->getIndexes() as $index) {
                print ' INDEX >>>'.$index->getName().' '.$index->isPrimary().' '.$index->isUnique().' '.PHP_EOL;
                foreach ($index->getColumns() as $column) {
                    print ' >>>> '.$column->getName().PHP_EOL;
                }
            }
            foreach ($table->getForeignKey() as $foreignKey) {
                print ' ForeignKey >>>'.$foreignKey->getName().PHP_EOL;
                foreach ($foreignKey->getColumns() as $column) {
                    print ' COLUMN >>>> '.$column->getName().PHP_EOL;
                }
                foreach ($foreignKey->getReferenceColumns() as $column) {
                    print ' TO '.$foreignKey->getReferenceTableName().' COLUMN >>>> '.$column->getName().PHP_EOL;
                }
            }
        }
    }
}
