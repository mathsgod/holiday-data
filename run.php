<?php

//download the file from the server
//https://www.1823.gov.hk/common/ical/en.json
$content = file_get_contents('https://www.1823.gov.hk/common/ical/en.json');

file_put_contents(__DIR__ . "/" . date('Y-m-d') . ".json", $content);
