<?php

use App\Parser\SearchQueryParser;

require 'vendor/autoload.php';

if ($argc < 2) {
    fprintf(STDERR, "invalid argument. example: ${argv[0]} QUERY\n");
    exit(1);
}

$parser = (new SearchQueryParser())->parse($argv[1]);

if (!empty($parser->getErrors())) {
    var_dump($parser->getErrors());
}

foreach ($parser->getExpressions() as $index => $expr) {
    $negative = $expr->negative ? 'true' : 'false';
    $target = $expr->target;
    $keyword = $expr->keyword;
    echo "Expr #$index | minus?=$negative target=$target keyword=$keyword\n";
}
