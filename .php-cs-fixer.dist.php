<?php

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->name('*.inc')
    ->in([__DIR__ . '/examples', __DIR__ . '/src', __DIR__ . '/tests']);

/**
 * The 'header_comment' validation was removed because the copyright year needs to be pinned to the year the file was
 * added to the repo.
 * @todo: Add a copyright validation (https://github.com/apigee/apigee-client-php/issues/81).
 */
$config = new PhpCsFixer\Config();

$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'class_definition' => ['single_line' => false, 'single_item_single_line' => true],
        'concat_space' => ['spacing' => 'one'],
        'general_phpdoc_annotation_remove' => ['annotations' => ['author']],
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_align' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_indent' => false,
        'general_phpdoc_tag_rename' => false,
        'phpdoc_inline_tag_normalizer' => false,
        'phpdoc_tag_type' => false,
        'phpdoc_order' => true,
        // Disabled because fluent setters return type in an interface can not be self.
        'self_accessor' => false,
        'void_return' => true,
        // Disabled because we want to keep all param and return tags.
        'no_superfluous_phpdoc_tags' => false,
        // Disabled because multiple lines allow code clarity.
        'single_line_throw' => false,
    ])
    ->setFinder($finder);

return $config;
