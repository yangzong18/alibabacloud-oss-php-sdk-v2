<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Agentic;

use AlibabaCloud\Oss\V2\Config;
use AlibabaCloud\Oss\V2\OperationInput;
use AlibabaCloud\Oss\V2\Types\BucketNameResolver;
use AlibabaCloud\Oss\V2\Types\EndpointProvider;
use AlibabaCloud\Oss\V2\Utils;
use Psr\Http\Message\UriInterface;

/**
 * Resolves the physical agentic bucket name and builds the virtual-hosted request
 * URL for it. A single instance is wired as both the {@see BucketNameResolver}
 * (used for signing) and the {@see EndpointProvider} (used for the request host).
 */
final class AgenticProvider implements EndpointProvider, BucketNameResolver
{
    /**
     * The base endpoint whose scheme and authority the request URL is built from.
     * @var UriInterface
     */
    private UriInterface $endpoint;

    /**
     * The account id used to derive the physical bucket name.
     * @var string
     */
    private string $accountId;

    /**
     * The region used to derive the physical bucket name.
     * @var string
     */
    private string $region;

    /**
     * The physical bucket name suffix, e.g. `ab-apsr` for agentic buckets or
     * `bs-apsr` for bucket spaces.
     * @var string
     */
    private string $suffix;

    /**
     * AgenticProvider constructor.
     * @param UriInterface $endpoint The base endpoint whose scheme and authority the request URL is built from.
     * @param string $accountId The account id used to derive the physical bucket name.
     * @param string $region The region used to derive the physical bucket name.
     * @param string $suffix The physical bucket name suffix (e.g. `ab-apsr` or `bs-apsr`).
     */
    public function __construct(UriInterface $endpoint, string $accountId, string $region, string $suffix)
    {
        $this->endpoint = $endpoint;
        $this->accountId = $accountId;
        $this->region = $region;
        $this->suffix = $suffix;
    }

    /**
     * Prepares the config and client options shared by the agentic clients: prefixes the
     * user agent with `agentic-client` and installs an `option_funcs` hook that wires an
     * {@see AgenticProvider} (bound to the given suffix) as both the endpoint provider and
     * the bucket name resolver once the endpoint has been resolved.
     * @param Config $config the source config; a clone is returned, the original is left untouched
     * @param array $options the client options to augment
     * @param string $suffix the physical bucket name suffix (e.g. `ab-apsr` or `bs-apsr`)
     * @return array{0: Config, 1: array} the cloned config and augmented options
     */
    public static function configureClientOptions(Config $config, array $options, string $suffix): array
    {
        $cfg = clone $config;
        $userAgent = 'agentic-client';
        if ($config->getUserAgent() != null) {
            $userAgent .= '/' . $config->getUserAgent();
        }
        $cfg->setUserAgent($userAgent);

        $accountId = $config->getAccountId() ?? '';
        $region = $config->getRegion() ?? '';
        $options['option_funcs'] = static function (array &$opts) use ($accountId, $region, $suffix) {
            if (!isset($opts['endpoint'])) {
                return;
            }
            $provider = new AgenticProvider($opts['endpoint'], $accountId, $region, $suffix);
            $opts['endpoint_provider'] = $provider;
            $opts['bucket_name_resolver'] = $provider;
        };

        return [$cfg, $options];
    }

    /**
     * Resolves the physical bucket name `{prefix}-{accountId}-{region}-{suffix}` from
     * the logical bucket carried by the input.
     * @param OperationInput $input
     * @return string|null the resolved physical bucket name, or null when the input carries no bucket
     */
    public function buildBucketName(OperationInput $input): ?string
    {
        $prefix = $input->getBucket();
        if ($prefix === null || $prefix === '') {
            return null;
        }
        return sprintf('%s-%s-%s-%s', $prefix, $this->accountId, $this->region, $this->suffix);
    }

    /**
     * Builds the virtual-hosted request URL. Bucket-scoped inputs route to the derived
     * bucket host `{physicalBucket}.{authority}`; inputs without a bucket route to the
     * bare endpoint authority.
     * @param OperationInput $input
     * @return string|null the request URL
     */
    public function buildUrl(OperationInput $input): ?string
    {
        $authority = $this->endpoint->getHost();
        if ($this->endpoint->getPort() !== null) {
            $authority .= ':' . $this->endpoint->getPort();
        }

        $bucket = $input->getBucket();
        if ($bucket === null || $bucket === '') {
            $host = $authority;
        } else {
            $host = $this->buildBucketName($input) . '.' . $authority;
        }

        $path = '';
        $key = $input->getKey();
        if ($key !== null && $key !== '') {
            $path = Utils::urlEncode($key, true);
        }

        return $this->endpoint->getScheme() . '://' . $host . '/' . $path;
    }
}
