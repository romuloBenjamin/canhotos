<?php
$pageDefault = new Pagebuilder();
$pageDefault->module = "public";
$pageDefault->folder = "template/defaults";
$pageDefault->file = "main";
echo "<body>";
$pageDefault->gerador_defaults();
echo "</body>";
