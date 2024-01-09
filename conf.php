<?php
$kasutaja='maksimtsepelevits';
$serverinimi='localhost';
$parool='123456';
$andmebaas='maksimtsepelevits';
$yhendus=new mysqli($serverinimi, $kasutaja, $parool, $andmebaas);
$yhendus->set_charset('UTF8');