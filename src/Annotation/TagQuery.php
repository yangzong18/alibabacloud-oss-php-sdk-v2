<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Annotation;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class TagQuery extends TagProperty
{
    public function __construct(string $rename, string $type, ?string $format = null)
    {
        parent::__construct('', 'query', $rename, $type, $format);
    }
}
