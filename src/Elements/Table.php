<?php
namespace TakaakiMizuno\MWBParser\Elements;

class Table extends Base
{
    /** @var string */
    protected $id;

    /** @var string $name */
    protected $name;

    /** @var \TakaakiMizuno\MWBParser\Elements\Column[] */
    protected $columns = [];

    /** @var \TakaakiMizuno\MWBParser\Elements\Index[] */
    protected $indexes = [];

    /** @var \TakaakiMizuno\MWBParser\Elements\ForeignKey[] */
    protected $foreignKeys = [];

    public function parse()
    {
        $this->id = (string) $this->object['id'];

        $this->parseName();
        $this->parseColumns();

        $ids = [];
        foreach ($this->columns as $column) {
            $ids[$column->getId()] = $column;
        }

        $this->parseIndexes($ids);
        $this->parseForeignKey($ids);
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

    protected function parseIndexes($ids)
    {
        $indexes = $this->object->xpath('.//value[@struct-name="db.mysql.Index"]');
        foreach ($indexes as $index) {
            $indexElement = new Index($index);
            $indexElement->resolveColumns($ids);
            $this->indexes[] = $indexElement;
        }
    }

    protected function parseForeignKey($ids)
    {
        $foreignKeys = $this->object->xpath('.//value[@struct-name="db.mysql.ForeignKey"]');
        foreach ($foreignKeys as $foreignKey) {
            $foreignKeyElement = new ForeignKey($foreignKey);
            $foreignKeyElement->resolveColumns($ids);
            $this->foreignKeys[] = $foreignKeyElement;
        }
    }

    /**
     * @param \TakaakiMizuno\MWBParser\Elements\Table[] $tables
     */
    public function resolveForeignKeyReference($tables)
    {
        foreach ($this->foreignKeys as $index => $foreignKey) {
            $this->foreignKeys[$index]->resolveReferencedTableAndColumn($tables);
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @param string $id
     *
     * @return null|\TakaakiMizuno\MWBParser\Elements\Column
     */
    public function getColumnById(string $id)
    {
        foreach ($this->columns as $column) {
            if ($column->getId() === $id) {
                return $column;
            }
        }

        return null;
    }
}
