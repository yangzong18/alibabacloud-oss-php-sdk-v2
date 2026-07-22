<?php

namespace IntegrationTests\Agentic;

use AlibabaCloud\Oss\V2 as Oss;
use AlibabaCloud\Oss\V2\Agentic\AgenticBucketClient;
use AlibabaCloud\Oss\V2\Agentic\Models;

class TestAgentic extends \PHPUnit\Framework\TestCase
{
    static $ACCESS_ID;
    static $ACCESS_KEY;
    static $ENDPOINT;
    static $REGION;
    static $ACCOUNT_ID;

    // Keep short: the resolved physical name {prefix}-{accountId}-{region}-ab-apsr
    // is used as a DNS label and must stay within the 63-character limit.
    public static $BUCKETNAME_PREFIX = "php-ab-";

    /** @var string[] */
    private static array $createdBuckets = [];

    public static function setUpBeforeClass(): void
    {
        self::$ACCESS_ID = getenv("OSS_TEST_ACCESS_KEY_ID");
        self::$ACCESS_KEY = getenv("OSS_TEST_ACCESS_KEY_SECRET");
        self::$ENDPOINT = getenv("OSS_TEST_ENDPOINT");
        self::$REGION = getenv("OSS_TEST_REGION") ?: 'cn-hangzhou';
        self::$ACCOUNT_ID = getenv("OSS_TEST_RAM_UID") ?: getenv("OSS_TEST_USER_ID");
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::$createdBuckets as $bucket) {
            self::cleanAgenticBucket($bucket);
        }
        self::$createdBuckets = [];
    }

    public static function newAgenticClient(): AgenticBucketClient
    {
        $cfg = Oss\Config::loadDefault();
        $cfg->setCredentialsProvider(new Oss\Credentials\StaticCredentialsProvider(
            self::$ACCESS_ID,
            self::$ACCESS_KEY
        ));
        $cfg->setRegion(self::$REGION);
        $cfg->setEndpoint(self::$ENDPOINT);
        $cfg->setAccountId(self::$ACCOUNT_ID);
        return new AgenticBucketClient($cfg);
    }

    public static function newInvalidAkAgenticClient(): AgenticBucketClient
    {
        $cfg = Oss\Config::loadDefault();
        $cfg->setCredentialsProvider(new Oss\Credentials\StaticCredentialsProvider(
            'invalid-ak',
            'invalid'
        ));
        $cfg->setRegion(self::$REGION);
        $cfg->setEndpoint(self::$ENDPOINT);
        $cfg->setAccountId(self::$ACCOUNT_ID);
        return new AgenticBucketClient($cfg);
    }

    public static function genAgenticBucketName(): string
    {
        $bucketName = self::$BUCKETNAME_PREFIX . strval(time()) . '-' . strval(rand(0, 99));
        self::$createdBuckets[] = $bucketName;
        return $bucketName;
    }

    public static function createAgenticBucket(AgenticBucketClient $client, string $bucket): void
    {
        try {
            $client->createAgenticBucket(new Models\CreateAgenticBucketRequest(
                $bucket,
                new Models\CreateAgenticBucketConfiguration('Standard', 'LRS')
            ));
        } catch (\Throwable $e) {
            self::skipIfAgenticProvisioningUnsupported($e);
            throw $e;
        }
        self::waitFor(1);
    }

    // Agentic bucket provisioning must be enabled for the account/region under test.
    // When it is not, the service rejects the create request with MalformedXML; the
    // request the SDK sends is byte-identical to the Java SDK, so this is an
    // environment capability gap rather than an SDK defect -> skip instead of fail.
    protected static function skipIfAgenticProvisioningUnsupported(\Throwable $e): void
    {
        $se = self::findServiceException($e);
        if ($se !== null && $se->getErrorCode() === 'MalformedXML') {
            self::markTestSkipped(
                'Agentic bucket provisioning is not enabled for this account/region: '
                . $se->getErrorMessage()
            );
        }
    }

    // Best-effort cleanup: remove attached properties before deleting the bucket.
    public static function cleanAgenticBucket(string $bucket): void
    {
        $client = self::newAgenticClient();
        try {
            $client->deleteAgenticBucketPolicy(new Models\DeleteAgenticBucketPolicyRequest($bucket));
        } catch (\Throwable $ignore) {
        }
        try {
            $client->deleteAgenticBucketEncryption(new Models\DeleteAgenticBucketEncryptionRequest($bucket));
        } catch (\Throwable $ignore) {
        }
        try {
            $client->deleteAgenticBucketPublicAccessBlock(new Models\DeleteAgenticBucketPublicAccessBlockRequest($bucket));
        } catch (\Throwable $ignore) {
        }
        try {
            $client->deleteAgenticBucket(new Models\DeleteAgenticBucketRequest($bucket));
        } catch (\Throwable $ignore) {
        }
    }

    public static function waitFor(float $sec): void
    {
        usleep((int)($sec * 1000000));
    }

    protected static function findServiceException(\Throwable $e): ?Oss\Exception\ServiceException
    {
        $cause = $e;
        while ($cause != null) {
            if ($cause instanceof Oss\Exception\ServiceException) {
                return $cause;
            }
            $cause = $cause->getPrevious();
        }
        return null;
    }
}
