<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['.'])
;

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'yoda_style' => false,
        // 'ordered_class_elements' => true,
        'phpdoc_to_comment' => false
    ])
;
