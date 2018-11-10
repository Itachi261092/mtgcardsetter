<?php
global $settings;
$settings = "settings.json";

function cfgRead() {
    global $settings;
    return json_decode(file_get_contents($settings), true);
}
function cfgWrite($data) {
    global $settings;
    $cfg = cfgRead();
    foreach ($data as $key => $oneParam){
        $cfg[$key] = $oneParam;
    }
    file_put_contents($settings, json_encode($cfg));
}