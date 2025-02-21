<?php

namespace IntegrationTests;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestIntegration.php';

use AlibabaCloud\Oss\V2 as Oss;

class ClientBucketWebsiteTest extends TestIntegration
{
    public function testBucketWebsite()
    {
        $client = $this->getDefaultClient();
        $bucketName = self::$bucketName;

        // PutBucketWebsite
        $putResult = $client->putBucketWebsite(new Oss\Models\PutBucketWebsiteRequest(
            $bucketName,
            websiteConfiguration: new Oss\Models\WebsiteConfiguration(
                indexDocument: new Oss\Models\IndexDocument(
                suffix: 'index.html', supportSubDir: true, type: 0
            ),
                errorDocument: new Oss\Models\ErrorDocument(
                    key: 'error.html', httpStatus: 404
                )
            )
        ));
        $this->assertEquals(200, $putResult->statusCode);
        $this->assertEquals('OK', $putResult->status);
        $this->assertEquals(True, count($putResult->headers) > 0);
        $this->assertEquals(24, strlen($putResult->requestId));

        // GetBucketWebsite
        $getResult = $client->getBucketWebsite(new Oss\Models\GetBucketWebsiteRequest(
            $bucketName
        ));
        $this->assertEquals(200, $getResult->statusCode);
        $this->assertEquals('OK', $getResult->status);
        $this->assertEquals(True, count($getResult->headers) > 0);
        $this->assertEquals(24, strlen($getResult->requestId));

        // DeleteBucketWebsite
        $delResult = $client->deleteBucketWebsite(new Oss\Models\DeleteBucketWebsiteRequest(
            $bucketName
        ));
        $this->assertEquals(204, $delResult->statusCode);
        $this->assertEquals('No Content', $delResult->status);
        $this->assertEquals(True, count($delResult->headers) > 0);
        $this->assertEquals(24, strlen($delResult->requestId));
    }

    public function testBucketWebsiteFail()
    {
        $client = $this->getDefaultClient();
        $bucketName = self::$bucketName . "-not-exist";

        // PutBucketWebsite
        try {
            $putResult = $client->putBucketWebsite(new Oss\Models\PutBucketWebsiteRequest(
                $bucketName,
                websiteConfiguration: new Oss\Models\WebsiteConfiguration(
                    indexDocument: new Oss\Models\IndexDocument(
                    suffix: 'index.html', supportSubDir: true, type: 0
                ),
                    errorDocument: new Oss\Models\ErrorDocument(
                        key: 'error.html', httpStatus: 404
                    )
                )
            ));
            $this->assertTrue(false, "should not here");
        } catch (Oss\Exception\OperationException $e) {
            $this->assertStringContainsString('Operation error PutBucketWebsite', $e);
            $se = $e->getPrevious();
            $this->assertInstanceOf(Oss\Exception\ServiceException::class, $se);
            if ($se instanceof Oss\Exception\ServiceException) {
                $this->assertEquals('NoSuchBucket', $se->getErrorCode());
                $this->assertEquals(404, $se->getStatusCode());
                $this->assertEquals(24, strlen($se->getRequestId()));
            }
        }

        // GetBucketWebsite
        try {
            $getResult = $client->getBucketWebsite(new Oss\Models\GetBucketWebsiteRequest(
                $bucketName
            ));
            $this->assertTrue(false, "should not here");
        } catch (Oss\Exception\OperationException $e) {
            $this->assertStringContainsString('Operation error GetBucketWebsite', $e);
            $se = $e->getPrevious();
            $this->assertInstanceOf(Oss\Exception\ServiceException::class, $se);
            if ($se instanceof Oss\Exception\ServiceException) {
                $this->assertEquals('NoSuchBucket', $se->getErrorCode());
                $this->assertEquals(404, $se->getStatusCode());
                $this->assertEquals(24, strlen($se->getRequestId()));
            }
        }

        // DeleteBucketWebsite
        try {
            $delResult = $client->deleteBucketWebsite(new Oss\Models\DeleteBucketWebsiteRequest(
                $bucketName
            ));
            $this->assertTrue(false, "should not here");
        } catch (Oss\Exception\OperationException $e) {
            $this->assertStringContainsString('Operation error DeleteBucketWebsite', $e);
            $se = $e->getPrevious();
            $this->assertInstanceOf(Oss\Exception\ServiceException::class, $se);
            if ($se instanceof Oss\Exception\ServiceException) {
                $this->assertEquals('NoSuchBucket', $se->getErrorCode());
                $this->assertEquals(404, $se->getStatusCode());
                $this->assertEquals(24, strlen($se->getRequestId()));
            }
        }
    }
}