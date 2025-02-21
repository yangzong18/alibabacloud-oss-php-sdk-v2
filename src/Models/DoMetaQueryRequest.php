<?php
declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Models;

use AlibabaCloud\Oss\V2\Types\RequestModel;
use AlibabaCloud\Oss\V2\Annotation\TagProperty;
use AlibabaCloud\Oss\V2\Annotation\RequiredProperty;

/**
 * The request for the DoMetaQuery operation.
 * Class DoMetaQueryRequest
 * @package AlibabaCloud\Oss\V2\Models
 */
final class DoMetaQueryRequest extends RequestModel
{
    /**
     * The name of the bucket.
     * @var string|null
     */
    #[RequiredProperty()]
    #[TagProperty(tag: '', position: 'host', rename: 'bucket', type: 'string')]
    public ?string $bucket;

    /**
     * The request body schema.
     * @var MetaQuery|null
     */
    #[RequiredProperty()]
    #[TagProperty(tag: '', position: 'body', rename: 'MetaQuery', type: 'xml')]
    public ?MetaQuery $metaQuery;

    /**
     * DoMetaQueryRequest constructor.
     * @param string|null $bucket The name of the bucket.
     * @param MetaQuery|null $metaQuery The request body schema.
     * @param array|null $options
     */
    public function __construct(
        ?string $bucket = null,
        ?MetaQuery $metaQuery = null,
        ?array $options = null
    )
    {
        $this->bucket = $bucket;
        $this->metaQuery = $metaQuery;
        parent::__construct($options);
    }
}