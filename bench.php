#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

function benchmark(string $type, int $times = 100000)
{
    echo 'Benchmarking ' . $type . "\n";

    $instance = setup($type);
    $data = [
        'data' => [
            'some', 'bits', 'to', 'iterate', 'over'
        ]
    ];

    // Prime the cache
    $result = benchmarkOnce($type, $instance, $data);

    echo "Result:\n";
    echo $result;

    $start = microtime(true);

    for ($i = 0; $i < $times; $i++) {
        benchmarkOnce($type, $instance, $data);
    }

    $end = microtime(true);

    echo "\n\n";
    echo "Time taken: " . ($end - $start) . "s\n";
    echo "Time taken per iteration: " . (($end - $start) * 1000 / $times) . "ms\n";
}

function setup($type)
{
    switch ($type) {
        case 'smarty':
            $smarty = new Smarty();
            $smarty->escape_html = true;
            $smarty->compile_check = false;
            $smarty->setCacheDir(__DIR__ . '/cache');
            $smarty->setCompileDir(__DIR__ . '/cache');
            return $smarty;
        case 'twig':
            $loader = new Twig_Loader_Filesystem('templates');

            return new Twig_Environment($loader, [
                'cache' => __DIR__ . '/cache',
                'auto_reload' => false,
            ]);

        case 'twig_reuse':
            $loader = new Twig_Loader_Filesystem('templates');

            $env = new Twig_Environment($loader, [
                'cache' => __DIR__ . '/cache',
                'auto_reload' => false,
            ]);

            return $env->load('index.html.twig');
        default:
            throw new InvalidArgumentException('Unknown type');
    }
}

function benchmarkOnce($type, $instance, $data)
{
    switch ($type) {
        case 'smarty':
            /** @var Smarty $instance */
            $instance->assign($data);
            return $instance->fetch('index.html.smarty');
        case 'twig':
            /** @var Twig_Environment $instance */
            $template = $instance->load('index.html.twig');
            return $template->render($data);

        case 'twig_reuse':
            /** @var Twig_TemplateWrapper $instance */
            return $instance->render($data);
        default:
            throw new InvalidArgumentException('Unknown type');
    }
}

function clear_cache(){
    return exec('rm -Rf cache/*');
}

$type = $argv[1] ?? null;
$times = $argv[2] ?? null;
isset($argv[3]) ? clear_cache() : null;


benchmark($type, $times);
