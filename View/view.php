<?php

class view
{

    protected $data = array();

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function show($page)
    {
        extract($this->data);
        include($page);
    }
}

?>