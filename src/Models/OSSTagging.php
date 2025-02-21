<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Models;

use AlibabaCloud\Oss\V2\Types\Model;
use AlibabaCloud\Oss\V2\Annotation\XmlElement;
use AlibabaCloud\Oss\V2\Annotation\XmlRoot;

/**
 * Class OSSTagging
 * @package AlibabaCloud\Oss\V2\Models
 */
#[XmlRoot(name: 'OSSTagging')]
final class OSSTagging extends Model
{
    /**
     * @var array<MetaQueryTagging>|null
     */
    #[XmlElement(rename: 'Tagging', type: MetaQueryTagging::class)]
    public ?array $taggings;

    /**
     * MetaQueryTagging constructor.
     * @param array<MetaQueryTagging>|null $taggings
     */
    public function __construct(
        ?array $taggings = null
    )
    {
        $this->taggings = $taggings;
    }
}