<?php

/*
 * Generated from resources/grammar/SearchQuery.g4 by ANTLR 4.13.1
 */

namespace App\Parser\SearchQuery;

use Antlr\Antlr4\Runtime\ParserRuleContext;
use Antlr\Antlr4\Runtime\Tree\ErrorNode;
use Antlr\Antlr4\Runtime\Tree\TerminalNode;

/**
 * This class provides an empty implementation of {@see SearchQueryListener},
 * which can be extended to create a listener which only needs to handle a subset
 * of the available methods.
 */
class SearchQueryBaseListener implements SearchQueryListener
{
    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function enterQuery(Context\QueryContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function exitQuery(Context\QueryContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function enterExpression(Context\ExpressionContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function exitExpression(Context\ExpressionContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function enterNegativeExpression(Context\NegativeExpressionContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function exitNegativeExpression(Context\NegativeExpressionContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function enterPositiveExpression(Context\PositiveExpressionContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function exitPositiveExpression(Context\PositiveExpressionContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function enterTarget(Context\TargetContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function exitTarget(Context\TargetContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function enterKeyword(Context\KeywordContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function exitKeyword(Context\KeywordContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function enterEveryRule(ParserRuleContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function exitEveryRule(ParserRuleContext $context): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function visitTerminal(TerminalNode $node): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation does nothing.
     */
    public function visitErrorNode(ErrorNode $node): void
    {
    }
}
