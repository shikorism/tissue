<?php

/*
 * Generated from resources/grammar/SearchQuery.g4 by ANTLR 4.13.1
 */

namespace App\Parser\SearchQuery;

use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;

/**
 * This interface defines a complete listener for a parse tree produced by
 * {@see SearchQueryParser}.
 */
interface SearchQueryListener extends ParseTreeListener
{
    /**
     * Enter a parse tree produced by {@see SearchQueryParser::query()}.
     * @param $context The parse tree.
     */
    public function enterQuery(Context\QueryContext $context): void;

    /**
     * Exit a parse tree produced by {@see SearchQueryParser::query()}.
     * @param $context The parse tree.
     */
    public function exitQuery(Context\QueryContext $context): void;

    /**
     * Enter a parse tree produced by {@see SearchQueryParser::expression()}.
     * @param $context The parse tree.
     */
    public function enterExpression(Context\ExpressionContext $context): void;

    /**
     * Exit a parse tree produced by {@see SearchQueryParser::expression()}.
     * @param $context The parse tree.
     */
    public function exitExpression(Context\ExpressionContext $context): void;

    /**
     * Enter a parse tree produced by {@see SearchQueryParser::negativeExpression()}.
     * @param $context The parse tree.
     */
    public function enterNegativeExpression(Context\NegativeExpressionContext $context): void;

    /**
     * Exit a parse tree produced by {@see SearchQueryParser::negativeExpression()}.
     * @param $context The parse tree.
     */
    public function exitNegativeExpression(Context\NegativeExpressionContext $context): void;

    /**
     * Enter a parse tree produced by {@see SearchQueryParser::positiveExpression()}.
     * @param $context The parse tree.
     */
    public function enterPositiveExpression(Context\PositiveExpressionContext $context): void;

    /**
     * Exit a parse tree produced by {@see SearchQueryParser::positiveExpression()}.
     * @param $context The parse tree.
     */
    public function exitPositiveExpression(Context\PositiveExpressionContext $context): void;

    /**
     * Enter a parse tree produced by {@see SearchQueryParser::target()}.
     * @param $context The parse tree.
     */
    public function enterTarget(Context\TargetContext $context): void;

    /**
     * Exit a parse tree produced by {@see SearchQueryParser::target()}.
     * @param $context The parse tree.
     */
    public function exitTarget(Context\TargetContext $context): void;

    /**
     * Enter a parse tree produced by {@see SearchQueryParser::keyword()}.
     * @param $context The parse tree.
     */
    public function enterKeyword(Context\KeywordContext $context): void;

    /**
     * Exit a parse tree produced by {@see SearchQueryParser::keyword()}.
     * @param $context The parse tree.
     */
    public function exitKeyword(Context\KeywordContext $context): void;
}
