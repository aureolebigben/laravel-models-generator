<?php

declare(strict_types=1);

namespace GiacomoMasseroni\LaravelModelsGenerator\Enums;

enum ColumnTypeEnum: string
{
    case INT = 'int';
    case FLOAT = 'float';
    case STRING = 'string';
    case BOOLEAN = 'boolean';
    case DATETIME = 'datetime';
    case IMMUTABLE_DATETIME = 'immutable_datetime';
    case ARRAY = 'array';
    case OBJECT = 'object';
    case COLLECTION = 'collection';
}
