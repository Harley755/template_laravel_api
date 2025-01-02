<?php

namespace App\Models;

use App\Traits\IsAppConfiguration;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class AppConfiguration extends Model implements HasMedia
{
    use InteractsWithMedia;
    use IsAppConfiguration;

    public $table = 'app_configurations';

    public const STRING_TYPE = "string";
    public const NUMERIC_TYPE = "number";
    public const BOOLEAN_TYPE = "boolean";
    public const ARRAY_TYPE = "array";
    public const JSON_TYPE = "json";

    public const ARRAY_SEPARATOR = ",";

    public const TYPES = [
        self::STRING_TYPE,
        self::NUMERIC_TYPE,
        self::BOOLEAN_TYPE,
        self::ARRAY_TYPE,
        self::JSON_TYPE,
    ];

    protected $fillable = [
        'code',
        'name',
        'value',
        'type',
        'visible',
        'description',
    ];

    public function getValueAttribute($value)
    {
        return $this->getValue($value);
    }
}
