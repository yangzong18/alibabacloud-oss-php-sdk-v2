<?php

namespace IntegrationTests\Agentic;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestAgentic.php';

use AlibabaCloud\Oss\V2 as Oss;
use AlibabaCloud\Oss\V2\Agentic\Models;
use AlibabaCloud\Oss\V2\Agentic\Paginator\ListAgenticBucketsPaginator;

class ClientAgenticBucketBasicTest extends TestAgentic
{
    public function testAgenticBucketLifecycle()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            // 1. Create agentic bucket
            try {
                $createResult = $client->createAgenticBucket(new Models\CreateAgenticBucketRequest(
                    $bucket,
                    new Models\CreateAgenticBucketConfiguration('Standard', 'LRS')
                ));
            } catch (\Throwable $e) {
                self::skipIfAgenticProvisioningUnsupported($e);
                throw $e;
            }
            $this->assertNotNull($createResult);
            $this->assertEquals(200, $createResult->statusCode);
            self::waitFor(1);

            // 2. Get agentic bucket
            $getResult = $client->getAgenticBucket(new Models\GetAgenticBucketRequest($bucket));
            $this->assertEquals(200, $getResult->statusCode);
            $this->assertNotNull($getResult->agenticBucketInfo);
            $this->assertStringContainsString($bucket, $getResult->agenticBucketInfo->name);

            // 3. List agentic buckets via paginator, verify the created bucket appears
            $found = false;
            $paginator = new ListAgenticBucketsPaginator($client);
            foreach ($paginator->iterPage(new Models\ListAgenticBucketsRequest()) as $page) {
                $this->assertEquals(200, $page->statusCode);
                foreach ($page->agenticBuckets ?? [] as $summary) {
                    if ($summary->name != null && strpos($summary->name, $bucket) !== false) {
                        $found = true;
                    }
                }
            }
            $this->assertTrue($found, "created agentic bucket should appear in list");

            // 4. Delete agentic bucket
            $deleteResult = $client->deleteAgenticBucket(new Models\DeleteAgenticBucketRequest($bucket));
            $this->assertTrue($deleteResult->statusCode == 200 || $deleteResult->statusCode == 204);
        } finally {
            self::cleanAgenticBucket($bucket);
        }
    }

    public function testPutAgenticBucketStatus()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            self::createAgenticBucket($client, $bucket);

            $putResult = $client->putAgenticBucketStatus(new Models\PutAgenticBucketStatusRequest(
                $bucket,
                new Models\AgenticBucketStatus('Enabled')
            ));
            $this->assertEquals(200, $putResult->statusCode);
        } finally {
            self::cleanAgenticBucket($bucket);
        }
    }

    public function testGetAgenticBucketNotExist()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            $client->getAgenticBucket(new Models\GetAgenticBucketRequest($bucket));
            $this->assertTrue(false, "should not here");
        } catch (\Throwable $ec) {
            $se = self::findServiceException($ec);
            $this->assertNotNull($se);
            $this->assertEquals(404, $se->getStatusCode());
        }
    }

    public function testAgenticBucketInvalidCredentials()
    {
        $client = self::newInvalidAkAgenticClient();
        $bucket = self::genAgenticBucketName();

        // Create with invalid AK
        try {
            $client->createAgenticBucket(new Models\CreateAgenticBucketRequest($bucket));
            $this->assertTrue(false, "should not here");
        } catch (\Throwable $ec) {
            $se = self::findServiceException($ec);
            $this->assertNotNull($se);
            $this->assertEquals(403, $se->getStatusCode());
            $this->assertNotEmpty($se->getRequestId());
        }

        // Get with invalid AK.
        // OSS checks bucket existence for a GET before validating the access key, so a
        // GET on a non-existent agentic bucket surfaces 404 (NoSuchBucket) rather than
        // 403; both indicate the request was rejected.
        try {
            $client->getAgenticBucket(new Models\GetAgenticBucketRequest($bucket));
            $this->assertTrue(false, "should not here");
        } catch (\Throwable $ec) {
            $se = self::findServiceException($ec);
            $this->assertNotNull($se);
            $this->assertContains($se->getStatusCode(), [403, 404]);
        }

        // List with invalid AK
        try {
            $client->listAgenticBuckets(new Models\ListAgenticBucketsRequest());
            $this->assertTrue(false, "should not here");
        } catch (\Throwable $ec) {
            $se = self::findServiceException($ec);
            $this->assertNotNull($se);
            $this->assertEquals(403, $se->getStatusCode());
        }
    }
}
