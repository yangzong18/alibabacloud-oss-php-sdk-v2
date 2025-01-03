<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Models;

use AlibabaCloud\Oss\V2\Types\Model;
use AlibabaCloud\Oss\V2\Annotation\XmlElement;
use AlibabaCloud\Oss\V2\Annotation\XmlRoot;

/**
 * Class UploadPart
 * @package AlibabaCloud\Oss\V2\Models
 */
#[XmlRoot(name: 'Part')]
final class UploadPart extends Model
{
    /**
     * The part number.
     * @var int|null
     */
    #[XmlElement(rename: 'PartNumber', type: 'int')]
    public ?int $partNumber;

    /**
     * The ETag value that is returned by OSS after the part is uploaded.
     * @var string|null
     */
    #[XmlElement(rename: 'ETag', type: 'string')]
    public ?string $etag;


    /**
     * UploadPart constructor.
     * @param int|null $partNumber The part number.
     * @param string|null $etag The ETag value that is returned by OSS after the part is uploaded.
     */
    public function __construct(
        ?int $partNumber = null,
        ?string $etag = null
    )
    {
        $this->partNumber = $partNumber;
        $this->etag = $etag;
    }
}
