<?php
namespace TakaakiMizuno\MWBParser\Elements;

class Table extends Base
{
    /** @var string $name */
    protected $name;

    /** @var \TakaakiMizuno\MWBParser\Elements\Column[] */
    protected $columns;

    /** @var \TakaakiMizuno\MWBParser\Elements\Index[] */
    protected $indexes;

    /** @var \TakaakiMizuno\MWBParser\Elements\ForeignKey[] */
    protected $foreignKeys;

    public function parse()
    {
        $this->parseName();
        $this->parseColumns();
        $this->parseIndexes();
        $this->parseForeignKey();
    }

    protected function parseName()
    {
        $this->name = $this->getValue('name');
    }

    protected function parseColumns()
    {
        $columns = $this->object->xpath('.//value[@struct-name="db.mysql.Column"]');
        foreach ($columns as $column) {
            $this->columns[] = new Column($column);
        }
    }

    protected function parseIndexes()
    {
        $ids = [];
        foreach ($this->columns as $column) {
            $ids[$column->getId()] = $column;
        }
        $indexes = $this->object->xpath('.//value[@struct-name="db.mysql.Index"]');
        foreach ($indexes as $index) {
            $indexElement = new Index($index);
            $indexElement->resolveColumns($ids);
            $this->indexes[] = new Index($index);
        }
    }

    protected function parseForeignKey()
    {
        $foreignKeys = $this->object->xpath('.//value[@struct-name="db.mysql.ForeignKey"]');
        foreach ($foreignKeys as $foreignKey) {
            $this->foreignKeys[] = new ForeignKey($foreignKey);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \TakaakiMizuno\MWBParser\Elements\Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return \TakaakiMizuno\MWBParser\Elements\Index[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @return \TakaakiMizuno\MWBParser\Elements\ForeignKey[]
     */
    public function getForeignKey()
    {
        return $this->foreignKeys;
    }
}
