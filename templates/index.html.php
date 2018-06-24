<?php
$this->layout('base.html', ['title' => 'document tittle']);
foreach($data as $row){
    echo $row."\n";
}
