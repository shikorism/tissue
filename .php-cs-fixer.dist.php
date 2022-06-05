<?php

return (new \PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'blank_line_before_return' => true,
        'function_typehint_space' => true,
        'method_separation' => true,
        'ordered_imports' => true,
        'return_type_declaration' => true,
        'new_with_braces' => true,
        'no_empty_statement' => true,
        'standardize_not_equals' => true,
        'single_quote' => true
    ])
    ->setFinder(
        \PhpCsFixer\Finder::create()
            ->exclude('bootstrap/cache')
            ->exclude('resources/views')
            ->exclude('storage')
            ->exclude('vendor')
            ->exclude('node_modules')
            ->in(__DIR__)
    );
