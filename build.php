#!/usr/bin/env php
<?php

$files = glob('src/*.php');
$output = array(
    "<?php\n\n",
    "define('PHPCOMPAT_VERSION', '0.1.0');\n\n",
);

$output_consts = array();
$output_funcs = array();
$output_classes = array();

foreach ($files as $file) {
    // handle constants
    if (preg_match('/consts\.(?P<prefix>[^.]+)\.php/', $file, $match)) {
        require $file;

        $consts = get_defined_constants();
        $prefix = 'PHPCOMPAT_' . strtoupper($match['prefix']) . '_';

        $count = 0;
        foreach ($consts as $name => $value) {
            if (0 === strncmp($name, $prefix, strlen($prefix))) {
                ++$count;
                $const = substr($name, strlen('PHPCOMPAT_'));
                $output_consts[] = sprintf("defined('%s') || define('%s', %s);\n", $const, $const, trim(var_export($value, true)));
            }
        }

        if ($count) {
            $output_consts[] = "\n";
        }
    }
    // handle functions
    elseif (preg_match('/function\.(?P<func>[^.]+)\.php/', $file, $match)) {
        require $file;
        $func = $match['func'];
        $output_funcs[] = sprintf("if (!function_exists('%s')) {\n", $func);

        $ref = new ReflectionFunction('phpcompat_' . $func);
        $doc = $ref->getDocComment();

        if (preg_match_all('/@uses\s+(?P<dep>[_a-z0-9]+\(\))/', $doc, $match)) {
            foreach ($match['dep'] as $dep) {
                $depRef = new ReflectionFunction(substr($dep, 0, -2));
                $output_funcs[] = render_body($depRef, 4);
                $output_funcs[] = "\n";
            }
        }

        $output_funcs[] = render_body($ref, 4);
        $output_funcs[] = "}\n\n";
    }

    elseif (preg_match('/class\.(?P<class>[^.]+)\.php/', $file, $match)) {
        require $file;

        $ref = new ReflectionClass('phpcompat_' . $match['class']);


        $class = substr($ref->getName(), strlen('phpcompat_'));

        if ($ref->isInterface()) {
            $output_classes[] = sprintf("if (!interface_exists('%s')) {\n", $class);
        } else {
            $output_classes[] = sprintf("if (!class_exists('%s')) {\n", $class);
        }
        $output_classes[] = render_body($ref, 4);
        $output_classes[] = "}\n\n";
    }
}

$compiled = implode('', array_merge(
    $output,
    $output_consts,
    $output_funcs,
    $output_classes
));

@mkdir('dist');
file_put_contents('dist/phpcompat.php', trim($compiled));

function render_docblock($ref, array $options = null)
{
    $lines = array_map(
        'trim',
        explode("\n", $ref->getDocComment())
    );

    $indent = isset($options['indent']) ? $options['indent'] : null;

    foreach ($lines as $i => $line) {
        // align stars in left column to first star of the first line
        if (substr($line, 0, 1) === '*') {
            $line = ' ' . $line;
        }
        if ($indent) {
            $line = $indent . $line;
        }
        $lines[$i] = $line;
    }

    if ($lines) {
        $lines[] = ''; // so that last line also ends with newline
        return implode("\n", $lines);
    }

    return '';
}

function render_body($ref, $ind = 0)
{
    $ind = str_repeat(' ', $ind);

    $filename = $ref->getFileName();
    $start_line = $ref->getStartLine() - 1; // -1 to include function()
    $end_line = $ref->getEndLine();
    $length = $end_line - $start_line;

    $source = file($filename);
    $source = array_slice($source, $start_line, $length);

    $source[0] =  str_replace(
        array(
            'function phpcompat_',
            'interface phpcompat_',
            'class phpcompat_',
        ),
        array(
            'function ',
            'interface ',
            'class '
        ),
        $source[0]
    );

    // remove indent
    $indent = '';
    if (preg_match('/^(?P<indent>\s+)/', $source[0], $match)) {
        $indent = $match['indent'];
    }

    if (strlen($indent)) {
        foreach ($source as $i => $line) {
            $line = str_replace("\t", '    ', $line);

            // remove current indent
            if (preg_match('/^\s{' . strlen($indent) .'}/', $line)) {
                $line = substr($line, strlen($indent));
            }

            $source[$i] = rtrim($line) . "\n";
        }
    }

    foreach ($source as $i => $line) {
        // add specified indent
        $source[$i] = $ind . $line;
    }

    $doc = render_docblock($ref, array(
        'indent' => $ind,
    ));
    return $doc . implode('', $source);
}
