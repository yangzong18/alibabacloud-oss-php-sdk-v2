<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Models;

use AlibabaCloud\Oss\V2\Types\Model;
use AlibabaCloud\Oss\V2\Annotation\XmlElement;
use AlibabaCloud\Oss\V2\Annotation\XmlRoot;

/**
 * Class MetaQueryFile
 * @package AlibabaCloud\Oss\V2\Models
 */
#[XmlRoot(name: 'File')]
final class MetaQueryFile extends Model
{
    /**
     * @var string|null
     */
    #[XmlElement(rename: 'Filename', type: 'string')]
    public ?string $filename;

    /**
     * @var string|null
     */
    #[XmlElement(rename: 'FileModifiedTime', type: 'string')]
    public ?string $fileModifiedTime;

    /**
     * @var string|null
     */
    #[XmlElement(rename: 'OSSStorageClass', type: 'string')]
    public ?string $ossStorageClass;

    /**
     * @var string|null
     */
    #[XmlElement(rename: 'ETag', type: 'string')]
    public ?string $etag;

    /**
     * @var string|null
     */
    #[XmlElement(rename: 'OSSCRC64', type: 'string')]
    public ?string $ossCrc64;

    /**
     * @var int|null
     */
    #[XmlElement(rename: 'OSSTaggingCount', type: 'int')]
    public ?int $ossTaggingCount;

    /**
     * @var int|null
     */
    #[XmlElement(rename: 'Size', type: 'int')]
    public ?int $size;

    /**
     * @var string|null
     */
    #[XmlElement(rename: 'OSSObjectType', type: 'string')]
    public ?string $ossObjectType;

    /**
     * @var string|null
     */
    #[XmlElement(rename: 'ObjectACL', type: 'string')]
    public ?string $objectACL;

    /**
     * @var string|null
     */
    #[XmlElement(rename: 'ServerSideEncryption', type: 'string')]
    public ?string $serverSideEncryption;

    /**
     * @var string|null
     */
    #[XmlElement(rename: 'ServerSideEncryptionCustomerAlgorithm', type: 'string')]
    public ?string $serverSideEncryptionCustomerAlgorithm;

    /**
     * @var OSSTagging|null
     */
    #[XmlElement(rename: 'OSSTagging', type: OSSTagging::class)]
    public ?OSSTagging $ossTagging;

    /**
     * @var MetaQueryOssUserMeta|null
     */
    #[XmlElement(rename: 'OSSUserMeta', type: MetaQueryOssUserMeta::class)]
    public ?MetaQueryOssUserMeta $ossUserMeta;

    /**
     * MetaQueryFile constructor.
     * @param string|null $filename
     * @param string|null $fileModifiedTime
     * @param string|null $ossStorageClass
     * @param string|null $etag
     * @param string|null $ossCrc64
     * @param int|null $ossTaggingCount
     * @param int|null $size
     * @param string|null $ossObjectType
     * @param string|null $objectACL
     * @param string|null $serverSideEncryption
     * @param string|null $serverSideEncryptionCustomerAlgorithm
     * @param OSSTagging|null $ossTagging
     * @param MetaQueryOssUserMeta|null $ossUserMeta
     */
    public function __construct(
        ?string $filename = null,
        ?string $fileModifiedTime = null,
        ?string $ossStorageClass = null,
        ?string $etag = null,
        ?string $ossCrc64 = null,
        ?int $ossTaggingCount = null,
        ?int $size = null,
        ?string $ossObjectType = null,
        ?string $objectACL = null,
        ?string $serverSideEncryption = null,
        ?string $serverSideEncryptionCustomerAlgorithm = null,
        ?OSSTagging $ossTagging = null,
        ?MetaQueryOssUserMeta $ossUserMeta = null
    )
    {
        $this->filename = $filename;
        $this->fileModifiedTime = $fileModifiedTime;
        $this->ossStorageClass = $ossStorageClass;
        $this->etag = $etag;
        $this->ossCrc64 = $ossCrc64;
        $this->ossTaggingCount = $ossTaggingCount;
        $this->size = $size;
        $this->ossObjectType = $ossObjectType;
        $this->objectACL = $objectACL;
        $this->serverSideEncryption = $serverSideEncryption;
        $this->serverSideEncryptionCustomerAlgorithm = $serverSideEncryptionCustomerAlgorithm;
        $this->ossTagging = $ossTagging;
        $this->ossUserMeta = $ossUserMeta;
    }
}