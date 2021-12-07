<?php

$finder = PhpCsFixer\Finder::create();

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony'                                      => true,
    '@Symfony:risky'                                => true,
    '@PHP71Migration'                               => true,
    '@PHP71Migration:risky'                         => true,
    '@DoctrineAnnotation'                           => true,
    '@PHPUnit60Migration:risky'                     => true,
    'align_multiline_comment'                       => ['comment_type' => 'phpdocs_like'],
    'array_indentation'                             => true,
    'array_syntax'                                  => ['syntax' => 'short'],
    'binary_operator_spaces'                        => [
        'default'   => 'align_single_space_minimal',
        'operators' => ['===' => 'single_space'],
    ],
    'compact_nullable_typehint'                     => true,
    'concat_space'                                  => ['spacing' => 'one'],
    'class_definition'                              => [
        'multi_line_extends_each_single_line' => true,
    ],
    'date_time_immutable'                           => true,
    'escape_implicit_backslashes'                   => true,
    'explicit_indirect_variable'                    => true,
    'explicit_string_variable'                      => true,
    'fopen_flags'                                   => ['b_mode' => true],
    'fully_qualified_strict_types'                  => true,
    'linebreak_after_opening_tag'                   => true,
    'list_syntax'                                   => ['syntax' => 'short'],
    'logical_operators'                             => true,
    'mb_str_functions'                              => true,
    'method_argument_space'                         => ['on_multiline' => 'ensure_fully_multiline'],
    'multiline_comment_opening_closing'             => true,
    'multiline_whitespace_before_semicolons'        => ['strategy' => 'new_line_for_chained_calls'],
    'native_function_invocation'                    => ['include' => ['@internal']],
    'no_alternative_syntax'                         => true,
    'no_binary_string'                              => true,
    'no_null_property_initialization'               => true,
    'no_php4_constructor'                           => true,
    'no_superfluous_elseif'                         => true,
    'no_superfluous_phpdoc_tags'                    => ['allow_mixed' => true],
    'no_unreachable_default_argument_value'         => true,
    'no_unset_on_property'                          => true,
    'no_useless_else'                               => true,
    'no_useless_return'                             => true,
    'ordered_class_elements'                        => true,
    'ordered_imports'                               => true,
    'php_unit_set_up_tear_down_visibility'          => true,
    'php_unit_strict'                               => true,
    'php_unit_test_annotation'                      => true,
    'php_unit_test_case_static_method_calls'        => true,
    'phpdoc_order'                                  => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types_order'                            => true,
    'simplified_null_return'                        => false,
    'single_line_throw'                             => false,
    'single_space_after_construct'                  => [
        // Remove 'implements' because long declaration lines must follow PSR-2
        'constructs' => [
            'abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'const', 'const_import',
            'continue', 'do', 'echo', 'else', 'elseif', 'extends', 'final', 'finally', 'for', 'foreach',
            'function', 'function_import', 'global', 'goto', 'if', 'include', 'include_once', 'instanceof',
            'insteadof', 'interface', 'match', 'new', 'open_tag_with_echo', 'php_open', 'print', 'private',
            'protected', 'public', 'require', 'require_once', 'return', 'static', 'throw', 'trait', 'try',
            'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from',
        ]
    ],
    'strict_comparison'                             => true,
    'strict_param'                                  => true,
    'string_line_ending'                            => true,
    'types_spaces'                                  => ['space' => 'single'],
])
->setRiskyAllowed(true)
->setFinder($finder);
