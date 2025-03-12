<?php

namespace App\Services\SchemaChange;

use PtOscCommandGenerator\Option;

class SchemaChangeService
{
    public const SUPPORTED_OPTIONS = [
        Option::ALTER_FOREIGN_KEYS_METHOD => [
            'type' => 'enum',
            'admitted_values' => [
                'auto' => 'auto (recommended)',
                'rebuild_constraints' => 'rebuild_constraints',
                'drop_swap' => 'drop_swap',
                'none' => 'none',
            ],
            'default_value' => 'auto',
        ],
        Option::ANALYZE_BEFORE_SWAP => [
            'type' => 'yesno',
            'yes_option' => Option::ANALYZE_BEFORE_SWAP,
            'no_option' => Option::NO_ANALYZE_BEFORE_SWAP,
            'default_value' => 'yes',
        ],
        Option::CHECK_ALTER => [
            'type' => 'yesno',
            'yes_option' => Option::CHECK_ALTER,
            'no_option' => Option::NO_CHECK_ALTER,
            'default_value' => 'yes',
        ],
        Option::CHECK_FOREIGN_KEYS => [
            'type' => 'yesno',
            'yes_option' => Option::CHECK_FOREIGN_KEYS,
            'no_option' => Option::NO_CHECK_FOREIGN_KEYS,
            'default_value' => 'yes',
        ],
        Option::CHECK_UNIQUE_KEY_CHANGE => [
            'type' => 'yesno',
            'yes_option' => Option::CHECK_UNIQUE_KEY_CHANGE,
            'no_option' => Option::NO_CHECK_UNIQUE_KEY_CHANGE,
            'default_value' => 'yes',
        ],
        Option::DROP_OLD_TABLE => [
            'type' => 'yesno',
            'yes_option' => Option::DROP_OLD_TABLE,
            'no_option' => Option::NO_DROP_OLD_TABLE,
            'default_value' => 'no',
        ],
        Option::NULL_TO_NOT_NULL => [
            'type' => 'flag',
            'default_value' => 'off',
        ],
        Option::MAX_LOAD => [
            'type' => 'string',
            'default_value' => 'Threads_running=25',
        ],
        Option::CRITICAL_LOAD => [
            'type' => 'string',
            'default_value' => 'Threads_running=50',
        ],
    ];
}
