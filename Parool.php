<?php
$parool="Maksim";
$parool2="opilane";
$cool="Ananas";
$krypt=crypt($parool, $cool);
$krypt2=crypt($parool2, $cool);
echo $krypt."<br>";
echo $krypt2;