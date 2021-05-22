<?php

use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\Error\Listeners\DiagnosticErrorListener;
use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\Tree\ParseTreeWalker;
use App\Parser\Context;
use App\Parser\SearchQueryLexer;
use App\Parser\SearchQueryParser;

require 'vendor/autoload.php';

class Listener extends \App\Parser\SearchQueryBaseListener
{
    const DEFAULT_TARGET = 'tag';

    const VALID_TARGETS = [
        'date',
        'link',
        'url',
        'note',
        'tag',
        'is', // -> boolean target
    ];

    private $expressions;
    private $workingExpression;

    public function __construct()
    {
        $this->expressions = [];
        $this->workingExpression = null;
    }

    public function getExpressions(): array
    {
        return $this->expressions;
    }

    public function enterExpression(Context\ExpressionContext $context): void
    {
        $this->workingExpression = [
            'negative' => false,
            'target' => null,
            'keyword' => null,
        ];
    }

    public function exitExpression(Context\ExpressionContext $context): void
    {
        if ($this->workingExpression['target'] === null) {
            $this->workingExpression['target'] = self::DEFAULT_TARGET;
        } elseif (!array_search($this->workingExpression['target'], self::VALID_TARGETS, true)) {
            // invalid target
            $prefix = $this->workingExpression['target'] . ':';
            $this->workingExpression['target'] = self::DEFAULT_TARGET;
            $this->workingExpression['keyword'] = $prefix . $this->workingExpression['keyword'];
        }

        $this->expressions[] = $this->workingExpression;
        $this->workingExpression = null;
    }

    public function enterNegativeExpression(Context\NegativeExpressionContext $context): void
    {
        $this->workingExpression['negative'] = true;
    }

    public function enterTarget(Context\TargetContext $context): void
    {
        $this->workingExpression['target'] = $context->WORD()->getText();
    }

    public function enterKeyword(Context\KeywordContext $context): void
    {
        if ($context->QUOTED_TEXT() !== null) {
            $quotedText = $context->QUOTED_TEXT()->getText();
            $this->workingExpression['keyword'] = substr($quotedText, 1, strlen($quotedText) - 2);
        } else {
            $this->workingExpression['keyword'] = $context->getText();
        }
    }
}

$input = <<<TEXT
巨乳 -奇乳 膣内射精 url:pixiv.net url:localhost:8080 link:"localhost.localdomain:4545" http://example.com/hoge
TEXT
;

if ($argc < 2) {
    fprintf(STDERR, "invalid argument. example: ${argv[0]} QUERY\n");
    exit(1);
}

$lexer = new SearchQueryLexer(InputStream::fromString($argv[1]));
$tokens = new CommonTokenStream($lexer);
$parser = new SearchQueryParser($tokens);
$parser->addErrorListener(new DiagnosticErrorListener());
$parser->setBuildParseTree(true);
$tree = $parser->query();

$listener = new Listener();
ParseTreeWalker::default()->walk($listener, $tree);

foreach ($listener->getExpressions() as $index => $expr) {
    $negative = $expr['negative'] ? 'true' : 'false';
    echo "Expr #$index | minus?=${negative} target=${expr['target']} keyword=${expr['keyword']} \n";
}
