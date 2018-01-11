<?php
namespace TakaakiMizuno\MWBParser\Elements;

use SebastianBergmann\CodeCoverage\Report\PHP;

class ForeignKey extends Base
{
    /** @var array */
    protected $attributes = [];

    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $many;

    /** @var \TakaakiMizuno\MWBParser\Elements\Column[] */
    protected $columns = [];

    /** @var string[] */
    protected $columnIds = [];

    /** @var \TakaakiMizuno\MWBParser\Elements\Column[] */
    protected $referencedColumns = [];

    /** @var string[] */
    protected $referencedColumnIds = [];

    /** @var string */
    protected $referencedTableId;

    /** @var string */
    protected $referencedTableName;

    /** @var string */
    protected $updateRule;

    /** @var string */
    protected $deleteRule;

    /*
    <value type="object" struct-name="db.mysql.ForeignKey" id="2C5C91D3-AB27-47AE-89EB-E9DA8747507E" struct-checksum="0x70a8fc40">
      <link type="object" struct-name="db.mysql.Table" key="referencedTable">28BE9E92-7A45-4761-883C-C58397C635A5</link>
      <value _ptr_="0x6080006badc0" type="list" content-type="object" content-struct-name="db.Column" key="columns">
        <link type="object">1EDF6400-E78B-4BB7-AFF3-0E296FC5457A</link>
      </value>
      <value _ptr_="0x6080008a4bc0" type="dict" key="customData"/>
      <value type="int" key="deferability">0</value>
      <value type="string" key="deleteRule">NO ACTION</value>
      <link type="object" struct-name="db.Index" key="index">2AE0D0B6-FAAB-4215-AD69-3B24413E30E6</link>
      <value type="int" key="mandatory">1</value>
      <value type="int" key="many">1</value>
      <value type="int" key="modelOnly">0</value>
      <link type="object" struct-name="db.Table" key="owner">A10CE359-B4CD-4D50-A4F0-9705054C73B5</link>
      <value _ptr_="0x6080006bd6a0" type="list" content-type="object" content-struct-name="db.Column" key="referencedColumns">
        <link type="object">A07FB6A1-A013-4BE7-B832-0BC9DD18E645</link>
      </value>
      <value type="int" key="referencedMandatory">1</value>
      <value type="string" key="updateRule">NO ACTION</value>
      <value type="string" key="comment"></value>
      <value type="string" key="name">fk_branch_users_users</value>
      <value type="string" key="oldName">fk_branch_users_users</value>
    </value>

     */
    public function parse()
    {
        $this->id = (string) $this->object['id'];

        $this->columnIds = [];
        foreach ($this->object->value as $value) {
            if (in_array((string) $value['type'], ['int', 'string'])) {
                $this->attributes[(string) $value['key']] = (string) $value;
            }
        }

        $columns = $this->object->xpath('.//value[@key="columns"]');
        foreach ($columns as $column) {
            $ids = $column->xpath('link[@type="object"]');
            foreach ($ids as $id) {
                $this->columnIds[] = (string) ($id);
            }
        }

        $columns = $this->object->xpath('.//value[@key="referencedColumns"]');
        foreach ($columns as $column) {
            $ids = $column->xpath('link[@type="object"]');
            foreach ($ids as $id) {
                $this->referencedColumnIds[] = (string) ($id);
            }
        }

        $this->parseSpecificAttributes();
    }

    public function resolveColumns($columns)
    {
        foreach ($this->columnIds as $id) {
            if (array_key_exists($id, $columns)) {
                $this->columns[] = $columns[$id];
            }
        }
    }

    /**
     * @param \TakaakiMizuno\MWBParser\Elements\Table[] $tables
     */
    public function resolveReferencedTableAndColumn($tables)
    {
        if (array_key_exists($this->referencedTableId, $tables)) {
            $table                     = $tables[$this->referencedTableId];
            $this->referencedTableName = $table->getName();
        } else {
            $this->referencedTableName = '';
            $this->referencedColumns   = [];

            return;
        }

        foreach ($this->referencedColumnIds as $id) {
            $column = $table->getColumnById($id);
            if (!empty($column)) {
                $this->referencedColumns[] = $column;
            }
        }
    }

    protected function parseSpecificAttributes()
    {
        $this->name              = $this->getValue('name');
        $this->many              = (bool) $this->getValue('many');
        $this->referencedTableId = $this->getLink('referencedTable');
        $this->deleteRule        = (bool) $this->getValue('deleteRule');
        $this->updateRule        = (bool) $this->getValue('updateRule');
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \TakaakiMizuno\MWBParser\Elements\Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return \TakaakiMizuno\MWBParser\Elements\Column[]
     */
    public function getReferenceColumns(): array
    {
        return $this->referencedColumns;
    }

    /**
     * @return string
     */
    public function getReferenceTableName(): string
    {
        return $this->referencedTableName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function hasMany(): bool
    {
        return $this->many;
    }
}
