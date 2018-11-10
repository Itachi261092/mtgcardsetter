<?php
include 'functions.php';

$str = htmlspecialchars(stripslashes(trim($_POST['cardname'])));
$cfg = cfgRead();
if (!empty($str) && $cfg['last'] != $str){ // if cardname changed, use the api scryfall
    $cfg['card'] = $str;
    $cfg['url'] = 'https://api.scryfall.com/cards/named?format=image&set=&fuzzy='.$str;
    $cfg['last'] = $str;
    cfgWrite($cfg);
}
else
    cfgWrite($_POST);

echo $cfg['url'];