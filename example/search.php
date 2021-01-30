<?php

set_time_limit(0);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$wow = WhereOnWebsite\Factory\WOWFactory::create();

$textToSearch = $_POST['text'];
$url = $_POST['url'];
$limit = (int) $_POST['limit']; // max number of internal web pages to attempt to scan 

$output = $wow->searchText($textToSearch, $url, $limit);

if ( !empty($output->getMatched()) ) {

    echo '<strong>Found matches on the following URLs:</strong><br>';

    foreach ($output->getMatched() as $url) {
        echo $url . '<br>';
    }

} else {

    echo 'No matches. Try increasing the limit.';

}