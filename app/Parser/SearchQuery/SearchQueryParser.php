<?php

/*
 * Generated from resources/grammar/SearchQuery.g4 by ANTLR 4.13.1
 */

namespace App\Parser\SearchQuery {
    use Antlr\Antlr4\Runtime\Atn\ATN;
    use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
    use Antlr\Antlr4\Runtime\Atn\ParserATNSimulator;
    use Antlr\Antlr4\Runtime\Dfa\DFA;
    use Antlr\Antlr4\Runtime\Error\Exceptions\FailedPredicateException;
    use Antlr\Antlr4\Runtime\Error\Exceptions\NoViableAltException;
    use Antlr\Antlr4\Runtime\Error\Exceptions\RecognitionException;
    use Antlr\Antlr4\Runtime\Parser;
    use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
    use Antlr\Antlr4\Runtime\RuleContext;
    use Antlr\Antlr4\Runtime\RuntimeMetaData;
    use Antlr\Antlr4\Runtime\Token;
    use Antlr\Antlr4\Runtime\TokenStream;
    use Antlr\Antlr4\Runtime\Vocabulary;
    use Antlr\Antlr4\Runtime\VocabularyImpl;

    final class SearchQueryParser extends Parser
    {
        public const WORD = 1, QUOTED_TEXT = 2, NOT = 3, DELIMITER = 4, WS = 5;

        public const RULE_query = 0, RULE_expression = 1, RULE_negativeExpression = 2,
            RULE_positiveExpression = 3, RULE_target = 4, RULE_keyword = 5;

        /**
         * @var array<string>
         */
        public const RULE_NAMES = [
            'query', 'expression', 'negativeExpression', 'positiveExpression', 'target',
            'keyword'
        ];

        /**
         * @var array<string|null>
         */
        private const LITERAL_NAMES = [
            null, null, null, "'-'", "':'"
        ];

        /**
         * @var array<string>
         */
        private const SYMBOLIC_NAMES = [
            null, 'WORD', 'QUOTED_TEXT', 'NOT', 'DELIMITER', 'WS'
        ];

        private const SERIALIZED_ATN =
            [4, 1, 5, 52, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 2, 3, 7, 3, 2, 4, 7,
            4, 2, 5, 7, 5, 1, 0, 1, 0, 1, 0, 5, 0, 16, 8, 0, 10, 0, 12, 0, 19,
            9, 0, 1, 0, 1, 0, 1, 1, 1, 1, 3, 1, 25, 8, 1, 1, 2, 1, 2, 1, 2, 1,
            3, 1, 3, 1, 3, 1, 3, 3, 3, 34, 8, 3, 1, 4, 1, 4, 1, 4, 1, 5, 1, 5,
            1, 5, 5, 5, 42, 8, 5, 10, 5, 12, 5, 45, 9, 5, 1, 5, 1, 5, 1, 5, 3,
            5, 50, 8, 5, 1, 5, 0, 0, 6, 0, 2, 4, 6, 8, 10, 0, 0, 51, 0, 12, 1,
            0, 0, 0, 2, 24, 1, 0, 0, 0, 4, 26, 1, 0, 0, 0, 6, 33, 1, 0, 0, 0,
            8, 35, 1, 0, 0, 0, 10, 49, 1, 0, 0, 0, 12, 17, 3, 2, 1, 0, 13, 14,
            5, 5, 0, 0, 14, 16, 3, 2, 1, 0, 15, 13, 1, 0, 0, 0, 16, 19, 1, 0,
            0, 0, 17, 15, 1, 0, 0, 0, 17, 18, 1, 0, 0, 0, 18, 20, 1, 0, 0, 0,
            19, 17, 1, 0, 0, 0, 20, 21, 5, 0, 0, 1, 21, 1, 1, 0, 0, 0, 22, 25,
            3, 4, 2, 0, 23, 25, 3, 6, 3, 0, 24, 22, 1, 0, 0, 0, 24, 23, 1, 0,
            0, 0, 25, 3, 1, 0, 0, 0, 26, 27, 5, 3, 0, 0, 27, 28, 3, 6, 3, 0, 28,
            5, 1, 0, 0, 0, 29, 30, 3, 8, 4, 0, 30, 31, 3, 10, 5, 0, 31, 34, 1,
            0, 0, 0, 32, 34, 3, 10, 5, 0, 33, 29, 1, 0, 0, 0, 33, 32, 1, 0, 0,
            0, 34, 7, 1, 0, 0, 0, 35, 36, 5, 1, 0, 0, 36, 37, 5, 4, 0, 0, 37,
            9, 1, 0, 0, 0, 38, 43, 5, 1, 0, 0, 39, 40, 5, 4, 0, 0, 40, 42, 5,
            1, 0, 0, 41, 39, 1, 0, 0, 0, 42, 45, 1, 0, 0, 0, 43, 41, 1, 0, 0,
            0, 43, 44, 1, 0, 0, 0, 44, 50, 1, 0, 0, 0, 45, 43, 1, 0, 0, 0, 46,
            47, 5, 1, 0, 0, 47, 50, 5, 4, 0, 0, 48, 50, 5, 2, 0, 0, 49, 38, 1,
            0, 0, 0, 49, 46, 1, 0, 0, 0, 49, 48, 1, 0, 0, 0, 50, 11, 1, 0, 0,
            0, 5, 17, 24, 33, 43, 49];
        protected static $atn;
        protected static $decisionToDFA;
        protected static $sharedContextCache;

        public function __construct(TokenStream $input)
        {
            parent::__construct($input);

            self::initialize();

            $this->interp = new ParserATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
        }

        private static function initialize(): void
        {
            if (self::$atn !== null) {
                return;
            }

            RuntimeMetaData::checkVersion('4.13.1', RuntimeMetaData::VERSION);

            $atn = (new ATNDeserializer())->deserialize(self::SERIALIZED_ATN);

            $decisionToDFA = [];
            for ($i = 0, $count = $atn->getNumberOfDecisions(); $i < $count; $i++) {
                $decisionToDFA[] = new DFA($atn->getDecisionState($i), $i);
            }

            self::$atn = $atn;
            self::$decisionToDFA = $decisionToDFA;
            self::$sharedContextCache = new PredictionContextCache();
        }

        public function getGrammarFileName(): string
        {
            return 'SearchQuery.g4';
        }

        public function getRuleNames(): array
        {
            return self::RULE_NAMES;
        }

        public function getSerializedATN(): array
        {
            return self::SERIALIZED_ATN;
        }

        public function getATN(): ATN
        {
            return self::$atn;
        }

        public function getVocabulary(): Vocabulary
        {
            static $vocabulary;

            return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
        }

        /**
         * @throws RecognitionException
         */
        public function query(): Context\QueryContext
        {
            $localContext = new Context\QueryContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 0, self::RULE_query);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(12);
                $this->expression();
                $this->setState(17);
                $this->errorHandler->sync($this);

                $_la = $this->input->LA(1);
                while ($_la === self::WS) {
                    $this->setState(13);
                    $this->match(self::WS);
                    $this->setState(14);
                    $this->expression();
                    $this->setState(19);
                    $this->errorHandler->sync($this);
                    $_la = $this->input->LA(1);
                }
                $this->setState(20);
                $this->match(self::EOF);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function expression(): Context\ExpressionContext
        {
            $localContext = new Context\ExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 2, self::RULE_expression);

            try {
                $this->setState(24);
                $this->errorHandler->sync($this);

                switch ($this->input->LA(1)) {
                    case self::NOT:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(22);
                        $this->negativeExpression();
                        break;

                    case self::WORD:
                    case self::QUOTED_TEXT:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(23);
                        $this->positiveExpression();
                        break;

                    default:
                        throw new NoViableAltException($this);
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function negativeExpression(): Context\NegativeExpressionContext
        {
            $localContext = new Context\NegativeExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 4, self::RULE_negativeExpression);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(26);
                $this->match(self::NOT);
                $this->setState(27);
                $this->positiveExpression();
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function positiveExpression(): Context\PositiveExpressionContext
        {
            $localContext = new Context\PositiveExpressionContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 6, self::RULE_positiveExpression);

            try {
                $this->setState(33);
                $this->errorHandler->sync($this);

                switch ($this->getInterpreter()->adaptivePredict($this->input, 2, $this->ctx)) {
                    case 1:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(29);
                        $this->target();
                        $this->setState(30);
                        $this->keyword();
                        break;

                    case 2:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(32);
                        $this->keyword();
                        break;
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function target(): Context\TargetContext
        {
            $localContext = new Context\TargetContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 8, self::RULE_target);

            try {
                $this->enterOuterAlt($localContext, 1);
                $this->setState(35);
                $this->match(self::WORD);
                $this->setState(36);
                $this->match(self::DELIMITER);
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }

        /**
         * @throws RecognitionException
         */
        public function keyword(): Context\KeywordContext
        {
            $localContext = new Context\KeywordContext($this->ctx, $this->getState());

            $this->enterRule($localContext, 10, self::RULE_keyword);

            try {
                $this->setState(49);
                $this->errorHandler->sync($this);

                switch ($this->getInterpreter()->adaptivePredict($this->input, 4, $this->ctx)) {
                    case 1:
                        $this->enterOuterAlt($localContext, 1);
                        $this->setState(38);
                        $this->match(self::WORD);
                        $this->setState(43);
                        $this->errorHandler->sync($this);

                        $_la = $this->input->LA(1);
                        while ($_la === self::DELIMITER) {
                            $this->setState(39);
                            $this->match(self::DELIMITER);
                            $this->setState(40);
                            $this->match(self::WORD);
                            $this->setState(45);
                            $this->errorHandler->sync($this);
                            $_la = $this->input->LA(1);
                        }
                        break;

                    case 2:
                        $this->enterOuterAlt($localContext, 2);
                        $this->setState(46);
                        $this->match(self::WORD);
                        $this->setState(47);
                        $this->match(self::DELIMITER);
                        break;

                    case 3:
                        $this->enterOuterAlt($localContext, 3);
                        $this->setState(48);
                        $this->match(self::QUOTED_TEXT);
                        break;
                }
            } catch (RecognitionException $exception) {
                $localContext->exception = $exception;
                $this->errorHandler->reportError($this, $exception);
                $this->errorHandler->recover($this, $exception);
            } finally {
                $this->exitRule();
            }

            return $localContext;
        }
    }
}

namespace App\Parser\SearchQuery\Context {
    use Antlr\Antlr4\Runtime\ParserRuleContext;
    use Antlr\Antlr4\Runtime\Token;
    use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
    use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
    use Antlr\Antlr4\Runtime\Tree\TerminalNode;
    use App\Parser\SearchQuery\SearchQueryListener;
    use App\Parser\SearchQuery\SearchQueryParser;

    class QueryContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return SearchQueryParser::RULE_query;
        }

        /**
         * @return array<ExpressionContext>|ExpressionContext|null
         */
        public function expression(?int $index = null)
        {
            if ($index === null) {
                return $this->getTypedRuleContexts(ExpressionContext::class);
            }

            return $this->getTypedRuleContext(ExpressionContext::class, $index);
        }

        public function EOF(): ?TerminalNode
        {
            return $this->getToken(SearchQueryParser::EOF, 0);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function WS(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(SearchQueryParser::WS);
            }

            return $this->getToken(SearchQueryParser::WS, $index);
        }

        public function enterRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->enterQuery($this);
            }
        }

        public function exitRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->exitQuery($this);
            }
        }
    }

    class ExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return SearchQueryParser::RULE_expression;
        }

        public function negativeExpression(): ?NegativeExpressionContext
        {
            return $this->getTypedRuleContext(NegativeExpressionContext::class, 0);
        }

        public function positiveExpression(): ?PositiveExpressionContext
        {
            return $this->getTypedRuleContext(PositiveExpressionContext::class, 0);
        }

        public function enterRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->enterExpression($this);
            }
        }

        public function exitRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->exitExpression($this);
            }
        }
    }

    class NegativeExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return SearchQueryParser::RULE_negativeExpression;
        }

        public function NOT(): ?TerminalNode
        {
            return $this->getToken(SearchQueryParser::NOT, 0);
        }

        public function positiveExpression(): ?PositiveExpressionContext
        {
            return $this->getTypedRuleContext(PositiveExpressionContext::class, 0);
        }

        public function enterRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->enterNegativeExpression($this);
            }
        }

        public function exitRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->exitNegativeExpression($this);
            }
        }
    }

    class PositiveExpressionContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return SearchQueryParser::RULE_positiveExpression;
        }

        public function target(): ?TargetContext
        {
            return $this->getTypedRuleContext(TargetContext::class, 0);
        }

        public function keyword(): ?KeywordContext
        {
            return $this->getTypedRuleContext(KeywordContext::class, 0);
        }

        public function enterRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->enterPositiveExpression($this);
            }
        }

        public function exitRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->exitPositiveExpression($this);
            }
        }
    }

    class TargetContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return SearchQueryParser::RULE_target;
        }

        public function WORD(): ?TerminalNode
        {
            return $this->getToken(SearchQueryParser::WORD, 0);
        }

        public function DELIMITER(): ?TerminalNode
        {
            return $this->getToken(SearchQueryParser::DELIMITER, 0);
        }

        public function enterRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->enterTarget($this);
            }
        }

        public function exitRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->exitTarget($this);
            }
        }
    }

    class KeywordContext extends ParserRuleContext
    {
        public function __construct(?ParserRuleContext $parent, ?int $invokingState = null)
        {
            parent::__construct($parent, $invokingState);
        }

        public function getRuleIndex(): int
        {
            return SearchQueryParser::RULE_keyword;
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function WORD(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(SearchQueryParser::WORD);
            }

            return $this->getToken(SearchQueryParser::WORD, $index);
        }

        /**
         * @return array<TerminalNode>|TerminalNode|null
         */
        public function DELIMITER(?int $index = null)
        {
            if ($index === null) {
                return $this->getTokens(SearchQueryParser::DELIMITER);
            }

            return $this->getToken(SearchQueryParser::DELIMITER, $index);
        }

        public function QUOTED_TEXT(): ?TerminalNode
        {
            return $this->getToken(SearchQueryParser::QUOTED_TEXT, 0);
        }

        public function enterRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->enterKeyword($this);
            }
        }

        public function exitRule(ParseTreeListener $listener): void
        {
            if ($listener instanceof SearchQueryListener) {
                $listener->exitKeyword($this);
            }
        }
    }
}
