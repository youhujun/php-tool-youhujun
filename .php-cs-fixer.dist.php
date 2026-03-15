<?php
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        // 核心：数组缩进对齐（3.x版本靠这个规则保证数组格式）
        'array_indentation' => true,
        // 仅保留3.x支持的braces规则配置
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'next',
            'position_after_control_structures' => 'next', // 控制结构（if/for等）后换行
        ],
        'indentation_type' => true, // 强制空格缩进，禁止Tab
        'no_trailing_whitespace' => true, // 清理行尾空格
        'trim_array_spaces' => true, // 清理数组内多余空格
        'normalize_index_brace' => true, // 统一数组索引格式
    ])
    ->setFinder($finder)
    ->setIndent('    ') // 强制4个空格缩进（保证左侧对齐）
    ->setLineEnding("\n"); // 统一换行符