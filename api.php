<?php
// Assuming you installed from Composer:
require "vendor/autoload.php";
use PHPHtmlParser\Dom;

function pascalCase($string, $dontStrip = []){
    // Edited from: https://stackoverflow.com/a/34597644
    return ucwords(str_replace(' ', '', ucwords(preg_replace('/^a-z0-9'.implode('',$dontStrip).']+/', ' ', str_replace('.', '', $string)))));
}

function formatNums($string) {
    return intval(str_replace('+', '', str_replace(',', '', trim($string))));
}

$dom = new Dom;
$dom->loadFromUrl('https://www.worldometers.info/coronavirus/');
$rows = $dom->find('#main_table_countries_today')->find('tbody')[0]->find('tr');

$array = [];

foreach ($rows as $row) {
    $cols = $row->find('td');
    $c_array = [];
    $country = trim($cols[0]->text);
    if ($country == "") $country = trim($cols[0]->find('a')[0]->text);
    if ($country == "") $country = trim($cols[0]->find('span')[0]->text);
    $country = str_replace('&eacute;', 'e', $country);
    $c_array['country'] = $country;
    $c_array['total_cases'] = formatNums($cols[1]->text);
    $c_array['new_cases'] = formatNums($cols[2]->text);
    $c_array['total_deaths'] = formatNums($cols[3]->text);
    $c_array['new_deaths'] = formatNums($cols[4]->text);
    $c_array['total_recovered'] = formatNums($cols[5]->text);
    $c_array['active_cases'] = formatNums($cols[6]->text);
    $c_array['critical'] = formatNums($cols[7]->text);
    $array[pascalCase($country)] = $c_array;
}

header('Content-Type: application/json');
echo json_encode($array);