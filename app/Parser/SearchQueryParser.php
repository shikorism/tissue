<?php

namespace App\Parser;

use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\Error\Listeners\DiagnosticErrorListener;
use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\Tree\ErrorNode;
use Antlr\Antlr4\Runtime\Tree\ParseTreeWalker;
use App\Parser\SearchQuery\Context;
use App\Parser\SearchQuery\Expression;
use App\Parser\SearchQuery\SearchQueryBaseListener;
use App\Parser\SearchQuery\SearchQueryLexer;

class SearchQueryParser extends SearchQueryBaseListener
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

    /** @var Expression[] */
    private $expressions = [];

    /** @var Expression|null */
    private $workingExpression = null;

    /** @var ErrorNode[] */
    private $errors = [];

    public function getExpressions(): array
    {
        return $this->expressions;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function parse(string $query): self
    {
        $lexer = new SearchQueryLexer(InputStream::fromString($query));
        $tokens = new CommonTokenStream($lexer);
        $parser = new SearchQuery\SearchQueryParser($tokens);
        $parser->addErrorListener(new DiagnosticErrorListener());
        $parser->setBuildParseTree(true);
        $tree = $parser->query();

        ParseTreeWalker::default()->walk($this, $tree);

        return $this;
    }

    public function enterExpression(Context\ExpressionContext $context): void
    {
        $this->workingExpression = new Expression();
    }

    public function exitExpression(Context\ExpressionContext $context): void
    {
        if ($this->workingExpression->target === null) {
            $this->workingExpression->target = self::DEFAULT_TARGET;
        } elseif (!array_search($this->workingExpression->target, self::VALID_TARGETS, true)) {
            // invalid target
            $prefix = $this->workingExpression->target . ':';
            $this->workingExpression->target = self::DEFAULT_TARGET;
            $this->workingExpression->keyword = $prefix . $this->workingExpression->keyword;
        }

        $this->expressions[] = $this->workingExpression;
        $this->workingExpression = null;
    }

    public function enterNegativeExpression(Context\NegativeExpressionContext $context): void
    {
        $this->workingExpression->negative = true;
    }

    public function enterTarget(Context\TargetContext $context): void
    {
        $this->workingExpression->target = $context->WORD()->getText();
    }

    public function enterKeyword(Context\KeywordContext $context): void
    {
        if ($context->QUOTED_TEXT() !== null) {
            $quotedText = $context->QUOTED_TEXT()->getText();
            $this->workingExpression->keyword = substr($quotedText, 1, strlen($quotedText) - 2);
        } else {
            $this->workingExpression->keyword = $context->getText();
        }
    }

    public function visitErrorNode(ErrorNode $node): void
    {
        $this->errors[] = $node;
    }
}
