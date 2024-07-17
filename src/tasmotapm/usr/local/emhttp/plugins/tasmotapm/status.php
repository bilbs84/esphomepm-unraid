<?php
$esphomepm_cfg = parse_ini_file( "/boot/config/plugins/esphomepm/esphomepm.cfg" );
$tasmotapm_device_ip	= isset($tasmotapm_cfg['DEVICE_IP']) ? $tasmotapm_cfg['DEVICE_IP'] : "";
$tasmotapm_use_pass	= isset($tasmotapm_cfg['DEVICE_USE_PASS']) ? $tasmotapm_cfg['DEVICE_USE_PASS'] : "false";
$tasmotapm_device_user	= isset($tasmotapm_cfg['DEVICE_USER']) ? $tasmotapm_cfg['DEVICE_USER'] : "";
$tasmotapm_device_pass	= isset($tasmotapm_cfg['DEVICE_PASS']) ? $tasmotapm_cfg['DEVICE_PASS'] : "";
$tasmotapm_costs_price	= isset($tasmotapm_cfg['COSTS_PRICE']) ? $tasmotapm_cfg['COSTS_PRICE'] : "0.0";
$tasmotapm_costs_unit	= isset($tasmotapm_cfg['COSTS_UNIT']) ? $tasmotapm_cfg['COSTS_UNIT'] : "USD";
$esphome_device_ip = isset($esphomepm_cfg['DEVICE_IP']) ? $esphomepm_cfg['DEVICE_IP'] : "";

if ($esphome_device_ip == "") {
    die("ESPHome Device IP missing!");
}

// Define sensor endpoints
$sensors = [
    'Power' => "/sensor/power",
    'Voltage' => "/sensor/voltage",
    'Current' => "/sensor/current",
    'Today' => "/sensor/today",
    'Factor' => "/sensor/factor"
];

$json = [];
foreach ($sensors as $key => $endpoint) {
    $Url = "http://$esphome_device_ip$endpoint";

    // Set up the timeout options
    $options = [
        "http" => [
            "timeout" => 5 // 5 seconds timeout
        ]
    ];
    $context = stream_context_create($options);
    
    $datajson = @file_get_contents($Url, false, $context);
    
    if ($datajson === false) {
        $json[$key] = "Error fetching data"; // Handle error
    } else {
        $data = json_decode($datajson, true);
        $json[$key] = isset($data['state']) ? $data['state'] : null;
    }
}

$json['Costs_Price'] = $tasmotapm_costs_price;
$json['Costs_Unit'] = $tasmotapm_costs_unit;

header('Content-Type: application/json');
echo json_encode($json);

?>
