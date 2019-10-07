<?php

class CustomFields
{

    private $fields = [];
    public function __construct($fields = [])
    {
        $this->fields = $fields;
    }

    public function addFields($field = [])
    {
        $this->fields[] = $field;
    }
}