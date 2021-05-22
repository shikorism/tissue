<?php

/*
 * Generated from resources/grammar/SearchQuery.g4 by ANTLR 4.9.2
 */

namespace App\Parser {
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

        /**
         * @var string
         */
        private const SERIALIZED_ATN =
            "\u{3}\u{608B}\u{A72A}\u{8133}\u{B9ED}\u{417C}\u{3BE7}\u{7786}\u{5964}" .
            "\u{3}\u{7}\u{36}\u{4}\u{2}\u{9}\u{2}\u{4}\u{3}\u{9}\u{3}\u{4}\u{4}" .
            "\u{9}\u{4}\u{4}\u{5}\u{9}\u{5}\u{4}\u{6}\u{9}\u{6}\u{4}\u{7}\u{9}" .
            "\u{7}\u{3}\u{2}\u{3}\u{2}\u{3}\u{2}\u{7}\u{2}\u{12}\u{A}\u{2}\u{C}" .
            "\u{2}\u{E}\u{2}\u{15}\u{B}\u{2}\u{3}\u{2}\u{3}\u{2}\u{3}\u{3}\u{3}" .
            "\u{3}\u{5}\u{3}\u{1B}\u{A}\u{3}\u{3}\u{4}\u{3}\u{4}\u{3}\u{4}\u{3}" .
            "\u{5}\u{3}\u{5}\u{3}\u{5}\u{3}\u{5}\u{5}\u{5}\u{24}\u{A}\u{5}\u{3}" .
            "\u{6}\u{3}\u{6}\u{3}\u{6}\u{3}\u{7}\u{3}\u{7}\u{3}\u{7}\u{7}\u{7}" .
            "\u{2C}\u{A}\u{7}\u{C}\u{7}\u{E}\u{7}\u{2F}\u{B}\u{7}\u{3}\u{7}\u{3}" .
            "\u{7}\u{3}\u{7}\u{5}\u{7}\u{34}\u{A}\u{7}\u{3}\u{7}\u{2}\u{2}\u{8}" .
            "\u{2}\u{4}\u{6}\u{8}\u{A}\u{C}\u{2}\u{2}\u{2}\u{35}\u{2}\u{E}\u{3}" .
            "\u{2}\u{2}\u{2}\u{4}\u{1A}\u{3}\u{2}\u{2}\u{2}\u{6}\u{1C}\u{3}\u{2}" .
            "\u{2}\u{2}\u{8}\u{23}\u{3}\u{2}\u{2}\u{2}\u{A}\u{25}\u{3}\u{2}\u{2}" .
            "\u{2}\u{C}\u{33}\u{3}\u{2}\u{2}\u{2}\u{E}\u{13}\u{5}\u{4}\u{3}\u{2}" .
            "\u{F}\u{10}\u{7}\u{7}\u{2}\u{2}\u{10}\u{12}\u{5}\u{4}\u{3}\u{2}\u{11}" .
            "\u{F}\u{3}\u{2}\u{2}\u{2}\u{12}\u{15}\u{3}\u{2}\u{2}\u{2}\u{13}\u{11}" .
            "\u{3}\u{2}\u{2}\u{2}\u{13}\u{14}\u{3}\u{2}\u{2}\u{2}\u{14}\u{16}\u{3}" .
            "\u{2}\u{2}\u{2}\u{15}\u{13}\u{3}\u{2}\u{2}\u{2}\u{16}\u{17}\u{7}\u{2}" .
            "\u{2}\u{3}\u{17}\u{3}\u{3}\u{2}\u{2}\u{2}\u{18}\u{1B}\u{5}\u{6}\u{4}" .
            "\u{2}\u{19}\u{1B}\u{5}\u{8}\u{5}\u{2}\u{1A}\u{18}\u{3}\u{2}\u{2}\u{2}" .
            "\u{1A}\u{19}\u{3}\u{2}\u{2}\u{2}\u{1B}\u{5}\u{3}\u{2}\u{2}\u{2}\u{1C}" .
            "\u{1D}\u{7}\u{5}\u{2}\u{2}\u{1D}\u{1E}\u{5}\u{8}\u{5}\u{2}\u{1E}\u{7}" .
            "\u{3}\u{2}\u{2}\u{2}\u{1F}\u{20}\u{5}\u{A}\u{6}\u{2}\u{20}\u{21}\u{5}" .
            "\u{C}\u{7}\u{2}\u{21}\u{24}\u{3}\u{2}\u{2}\u{2}\u{22}\u{24}\u{5}\u{C}" .
            "\u{7}\u{2}\u{23}\u{1F}\u{3}\u{2}\u{2}\u{2}\u{23}\u{22}\u{3}\u{2}\u{2}" .
            "\u{2}\u{24}\u{9}\u{3}\u{2}\u{2}\u{2}\u{25}\u{26}\u{7}\u{3}\u{2}\u{2}" .
            "\u{26}\u{27}\u{7}\u{6}\u{2}\u{2}\u{27}\u{B}\u{3}\u{2}\u{2}\u{2}\u{28}" .
            "\u{2D}\u{7}\u{3}\u{2}\u{2}\u{29}\u{2A}\u{7}\u{6}\u{2}\u{2}\u{2A}\u{2C}" .
            "\u{7}\u{3}\u{2}\u{2}\u{2B}\u{29}\u{3}\u{2}\u{2}\u{2}\u{2C}\u{2F}\u{3}" .
            "\u{2}\u{2}\u{2}\u{2D}\u{2B}\u{3}\u{2}\u{2}\u{2}\u{2D}\u{2E}\u{3}\u{2}" .
            "\u{2}\u{2}\u{2E}\u{34}\u{3}\u{2}\u{2}\u{2}\u{2F}\u{2D}\u{3}\u{2}\u{2}" .
            "\u{2}\u{30}\u{31}\u{7}\u{3}\u{2}\u{2}\u{31}\u{34}\u{7}\u{6}\u{2}\u{2}" .
            "\u{32}\u{34}\u{7}\u{4}\u{2}\u{2}\u{33}\u{28}\u{3}\u{2}\u{2}\u{2}\u{33}" .
            "\u{30}\u{3}\u{2}\u{2}\u{2}\u{33}\u{32}\u{3}\u{2}\u{2}\u{2}\u{34}\u{D}" .
            "\u{3}\u{2}\u{2}\u{2}\u{7}\u{13}\u{1A}\u{23}\u{2D}\u{33}";

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

            RuntimeMetaData::checkVersion('4.9.2', RuntimeMetaData::VERSION);

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

        public function getSerializedATN(): string
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

namespace App\Parser\Context {
    use Antlr\Antlr4\Runtime\ParserRuleContext;
    use Antlr\Antlr4\Runtime\Token;
    use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
    use Antlr\Antlr4\Runtime\Tree\ParseTreeVisitor;
    use Antlr\Antlr4\Runtime\Tree\TerminalNode;
    use App\Parser\SearchQueryListener;
    use App\Parser\SearchQueryParser;

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
