<?php
namespace TakaakiMizuno\MWBParser\Elements;

class Index extends Base
{
    /*
                    <value type="object" struct-name="db.mysql.Index" id="CCD06A86-624A-42B7-9D5C-671910E32FAE" struct-checksum="0xb3a154c1">
                      <value type="string" key="algorithm"></value>
                      <value _ptr_="0x600000aa05a0" type="list" content-type="object" content-struct-name="db.mysql.IndexColumn" key="columns">
                        <value type="object" struct-name="db.mysql.IndexColumn" id="4D94CD1A-3027-41F0-97B4-2E1E13A964AB" struct-checksum="0x62630b3c">
                          <value type="int" key="columnLength">0</value>
                          <value type="string" key="comment"></value>
                          <value type="int" key="descend">0</value>
                          <link type="object" struct-name="db.Column" key="referencedColumn">A07FB6A1-A013-4BE7-B832-0BC9DD18E645</link>
                          <value type="string" key="name"></value>
                          <link type="object" struct-name="GrtObject" key="owner">CCD06A86-624A-42B7-9D5C-671910E32FAE</link>
                        </value>
                      </value>
                      <value type="string" key="indexKind"></value>
                      <value type="int" key="keyBlockSize">0</value>
                      <value type="string" key="lockOption"></value>
                      <value type="string" key="withParser"></value>
                      <value type="string" key="comment"></value>
                      <value type="int" key="deferability">0</value>
                      <value type="string" key="indexType">PRIMARY</value>
                      <value type="int" key="isPrimary">1</value>
                      <value type="string" key="name">PRIMARY</value>
                      <value type="int" key="unique">0</value>
                      <value type="int" key="commentedOut">0</value>
                      <value type="string" key="createDate"></value>
                      <value _ptr_="0x6000002bc500" type="dict" key="customData"/>
                      <value type="string" key="lastChangeDate"></value>
                      <value type="int" key="modelOnly">0</value>
                      <link type="object" struct-name="GrtNamedObject" key="owner">28BE9E92-7A45-4761-883C-C58397C635A5</link>
                      <value type="string" key="temp_sql"></value>
                      <value type="string" key="oldName">PRIMARY</value>
                    </value>

     */
    protected $attributes;

    /** @var array */
    protected $columns;

    /** @var array */
    protected $columnIds;

    public function parse()
    {
        $this->columnIds = [];
        foreach ($this->object->value as $value) {
            if (in_array((string) $value['type'], ['int', 'string'])) {
                $this->attributes[(string) $value['key']] = (string) $value;
            }
            $columns = $this->object->xpath('//value[@struct-name="db.mysql.IndexColumn"]');
            foreach ($columns as $column) {
                $ids = $column->xpath('link[@struct-name="db.Column"]');
                if (count($ids) > 0) {
                    $this->columnIds[] = (string) ($ids[0]);
                }
            }
        }
    }

    public function resolveColumns($columns)
    {
        foreach ($this->columnIds as $id) {
            if (array_key_exists($id, $columns)) {
                $this->columns = $columns[$id];
            }
        }
    }
}
