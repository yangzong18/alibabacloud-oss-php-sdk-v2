<?php

namespace IntegrationTests\Agentic;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestAgentic.php';

use AlibabaCloud\Oss\V2\Agentic\Models;

class ClientAgenticBucketSpaceTest extends TestAgentic
{
    public function testListBucketSpaces()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            self::createAgenticBucket($client, $bucket);

            $listResult = $client->listBucketSpaces(new Models\ListBucketSpacesRequest($bucket));
            $this->assertNotNull($listResult);
            $this->assertEquals(200, $listResult->statusCode);
        } finally {
            self::cleanAgenticBucket($bucket);
        }
    }
}
