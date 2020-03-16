<?php
// Assuming you installed from Composer:
require "vendor/autoload.php";
use PHPHtmlParser\Dom;

function pascalCase($string, $dontStrip = []){
    // Edited from: https://stackoverflow.com/a/34597644
    return ucwords(str_replace(' ', '', ucwords(preg_replace('/^a-z0-9'.implode('',$dontStrip).']+/', ' ', str_replace('.', '', $string)))));
}

$dom = new Dom;
$dom->loadFromUrl('https://www.worldometers.info/coronavirus/');
$rows = $dom->find('#main_table_countries')->find('tbody')[0]->find('tr');

$array = [];

foreach ($rows as $row) {
    $cols = $row->find('td');
    $c_array = [];
    $country = trim($cols[0]->text);
    if ($country == "") $country = trim($cols[0]->find('a')[0]->text);
    if ($country == "") $country = trim($cols[0]->find('span')[0]->text);
    $country = str_replace('&eacute;', 'e', $country);
    $c_array['country'] = $country;
    $c_array['total_cases'] = trim($cols[1]->text);
    $c_array['new_cases'] = trim($cols[2]->text);
    $c_array['total_deaths'] = trim($cols[3]->text);
    $c_array['new_deaths'] = trim($cols[4]->text);
    $c_array['total_recovered'] = trim($cols[5]->text);
    $c_array['active_cases'] = trim($cols[6]->text);
    $c_array['critical'] = trim($cols[7]->text);
    $array[pascalCase($country)] = $c_array;
}

header('Content-Type: application/json');
echo json_encode($array);