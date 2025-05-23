<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AlibabaCloud\Oss\V2 as Oss;

// parse args
$optsdesc = [
    "region" => ['help'=>'The region in which the bucket is located.', 'required' => True],
    "endpoint" => ['help'=>'The domain names that other services can use to access OSS.', 'required' => False],
];
$longopts = \array_map(function($key){return "$key:";}, array_keys($optsdesc));
$options = getopt("", $longopts);
foreach ($optsdesc as $key => $value) {
    if ($value['required'] === True && empty($options[$key])) {
        $help = $value['help'];
        echo "Error: the following arguments are required: --$key, $help";
        exit(1);
    }
}

$region = $options["region"];

// Loading credentials values from the environment variables
$credentialsProvider = new Oss\Credentials\EnvironmentVariableCredentialsProvider();

// Using the SDK's default configuration
$cfg = Oss\Config::loadDefault();
$cfg->setCredentialsProvider($credentialsProvider);
$cfg->setRegion($region);
if (isset($options["endpoint"])) {
    $cfg->setEndpoint($options["endpoint"]);
}

$client = new Oss\Client($cfg);

// Create the Paginator for the ListBuckets operation
$paginator = new Oss\Paginator\ListBucketsPaginator($client);
$iter = $paginator->iterPage(new Oss\Models\ListBucketsRequest());

// Iterate through the bucket pages
foreach ($iter as $page) {
    foreach ($page->buckets ?? [] as $bucket) {
        print ("Bucket: $bucket->name, $bucket->location\n");
    }
}
