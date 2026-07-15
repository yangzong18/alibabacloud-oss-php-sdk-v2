<?php
declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Agentic\Models;

use AlibabaCloud\Oss\V2\Types\RequestModel;
use AlibabaCloud\Oss\V2\Annotation\TagProperty;
use AlibabaCloud\Oss\V2\Annotation\RequiredProperty;

/**
 * The request for the ListBucketSpaces operation.
 * Class ListBucketSpacesRequest
 * @package AlibabaCloud\Oss\V2\Agentic\Models
 */
final class ListBucketSpacesRequest extends RequestModel
{
    /**
     * The name of the bucket.
     * @var string|null
     */
    #[RequiredProperty()]
    #[TagProperty(tag: '', position: 'host', rename: 'bucket', type: 'string')]
    public ?string $bucket;

    /**
     * The prefix that the names of returned bucket spaces must contain.
     * @var string|null
     */
    #[TagProperty(tag: '', position: 'query', rename: 'prefix', type: 'string')]
    public ?string $prefix;

    /**
     * The token from which the list operation must start.
     * @var string|null
     */
    #[TagProperty(tag: '', position: 'query', rename: 'continuation-token', type: 'string')]
    public ?string $continuationToken;

    /**
     * The maximum number of bucket spaces that can be returned.
     * @var int|null
     */
    #[TagProperty(tag: '', position: 'query', rename: 'max-keys', type: 'int')]
    public ?int $maxKeys;

    /**
     * ListBucketSpacesRequest constructor.
     * @param string|null $bucket The name of the bucket.
     * @param string|null $prefix The prefix that the names of returned bucket spaces must contain.
     * @param string|null $continuationToken The token from which the list operation must start.
     * @param int|null $maxKeys The maximum number of bucket spaces that can be returned.
     * @param array|null $options
     */
    public function __construct(
        ?string $bucket = null,
        ?string $prefix = null,
        ?string $continuationToken = null,
        ?int $maxKeys = null,
        ?array $options = null
    )
    {
        $this->bucket = $bucket;
        $this->prefix = $prefix;
        $this->continuationToken = $continuationToken;
        $this->maxKeys = $maxKeys;
        parent::__construct($options);
    }
}
