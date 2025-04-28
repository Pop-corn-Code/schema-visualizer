<?php

function getRelationSymbol(string $type): string
{
    return match($type) {
        'HasOne' => '|o',
        'HasMany' => '}o',
        'BelongsTo' => 'o|',
        'BelongsToMany' => '}o',
        'MorphTo' => 'o|',
        'MorphMany' => '}o',
        default => '}|',
    };
}
