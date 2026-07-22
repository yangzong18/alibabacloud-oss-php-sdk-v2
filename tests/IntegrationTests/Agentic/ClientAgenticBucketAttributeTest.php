<?php

namespace IntegrationTests\Agentic;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestAgentic.php';

use AlibabaCloud\Oss\V2 as Oss;
use AlibabaCloud\Oss\V2\Agentic\Models;
use AlibabaCloud\Oss\V2\Models\ApplyServerSideEncryptionByDefault;
use AlibabaCloud\Oss\V2\Models\PublicAccessBlockConfiguration;
use AlibabaCloud\Oss\V2\Models\ServerSideEncryptionRule;
use AlibabaCloud\Oss\V2\Models\VersioningConfiguration;

class ClientAgenticBucketAttributeTest extends TestAgentic
{
    public function testAgenticBucketAcl()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            self::createAgenticBucket($client, $bucket);

            // Put ACL
            $putResult = $client->putAgenticBucketAcl(new Models\PutAgenticBucketAclRequest($bucket, 'private'));
            $this->assertEquals(200, $putResult->statusCode);

            // Get ACL
            $getResult = $client->getAgenticBucketAcl(new Models\GetAgenticBucketAclRequest($bucket));
            $this->assertEquals(200, $getResult->statusCode);
            $this->assertNotNull($getResult->accessControlPolicy);
            $this->assertNotNull($getResult->accessControlPolicy->accessControlList);
            $this->assertEquals('private', $getResult->accessControlPolicy->accessControlList->grant);
        } finally {
            self::cleanAgenticBucket($bucket);
        }
    }

    public function testAgenticBucketEncryption()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            self::createAgenticBucket($client, $bucket);

            // Put encryption
            $putResult = $client->putAgenticBucketEncryption(new Models\PutAgenticBucketEncryptionRequest(
                $bucket,
                new ServerSideEncryptionRule(new ApplyServerSideEncryptionByDefault(sseAlgorithm: 'AES256'))
            ));
            $this->assertEquals(200, $putResult->statusCode);

            // Get encryption
            $getResult = $client->getAgenticBucketEncryption(new Models\GetAgenticBucketEncryptionRequest($bucket));
            $this->assertEquals(200, $getResult->statusCode);
            $this->assertNotNull($getResult->serverSideEncryptionRule);
            $this->assertNotNull($getResult->serverSideEncryptionRule->applyServerSideEncryptionByDefault);
            $this->assertEquals(
                'AES256',
                $getResult->serverSideEncryptionRule->applyServerSideEncryptionByDefault->sseAlgorithm
            );

            // Delete encryption
            $deleteResult = $client->deleteAgenticBucketEncryption(new Models\DeleteAgenticBucketEncryptionRequest($bucket));
            $this->assertTrue($deleteResult->statusCode == 200 || $deleteResult->statusCode == 204);
        } finally {
            self::cleanAgenticBucket($bucket);
        }
    }

    public function testAgenticBucketVersioning()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            self::createAgenticBucket($client, $bucket);

            // Put versioning
            $putResult = $client->putAgenticBucketVersioning(new Models\PutAgenticBucketVersioningRequest(
                $bucket,
                new VersioningConfiguration('Enabled')
            ));
            $this->assertEquals(200, $putResult->statusCode);

            // Get versioning
            $getResult = $client->getAgenticBucketVersioning(new Models\GetAgenticBucketVersioningRequest($bucket));
            $this->assertEquals(200, $getResult->statusCode);
            $this->assertNotNull($getResult->versioningConfiguration);
            $this->assertEquals('Enabled', $getResult->versioningConfiguration->status);
        } finally {
            self::cleanAgenticBucket($bucket);
        }
    }

    public function testAgenticBucketPolicy()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            self::createAgenticBucket($client, $bucket);

            $policy = '{"Version":"1","Statement":[{"Effect":"Allow",' .
                '"Action":["oss:GetObject"],"Principal":["*"],' .
                '"Resource":["acs:oss:*:' . self::$ACCOUNT_ID . ':*"]}]}';

            // Put policy
            $putResult = $client->putAgenticBucketPolicy(new Models\PutAgenticBucketPolicyRequest($bucket, $policy));
            $this->assertEquals(200, $putResult->statusCode);

            // Get policy
            $getResult = $client->getAgenticBucketPolicy(new Models\GetAgenticBucketPolicyRequest($bucket));
            $this->assertEquals(200, $getResult->statusCode);
            $this->assertStringContainsString('oss:GetObject', $getResult->policy);

            // Delete policy
            $deleteResult = $client->deleteAgenticBucketPolicy(new Models\DeleteAgenticBucketPolicyRequest($bucket));
            $this->assertTrue($deleteResult->statusCode == 200 || $deleteResult->statusCode == 204);
        } finally {
            self::cleanAgenticBucket($bucket);
        }
    }

    public function testAgenticBucketPublicAccessBlock()
    {
        $client = self::newAgenticClient();
        $bucket = self::genAgenticBucketName();

        try {
            self::createAgenticBucket($client, $bucket);

            // Put public access block
            $putResult = $client->putAgenticBucketPublicAccessBlock(new Models\PutAgenticBucketPublicAccessBlockRequest(
                $bucket,
                new PublicAccessBlockConfiguration(true)
            ));
            $this->assertEquals(200, $putResult->statusCode);

            // Get public access block
            $getResult = $client->getAgenticBucketPublicAccessBlock(new Models\GetAgenticBucketPublicAccessBlockRequest($bucket));
            $this->assertEquals(200, $getResult->statusCode);
            $this->assertNotNull($getResult->publicAccessBlockConfiguration);

            // Delete public access block
            $deleteResult = $client->deleteAgenticBucketPublicAccessBlock(new Models\DeleteAgenticBucketPublicAccessBlockRequest($bucket));
            $this->assertTrue($deleteResult->statusCode == 200 || $deleteResult->statusCode == 204);
        } finally {
            self::cleanAgenticBucket($bucket);
        }
    }
}
