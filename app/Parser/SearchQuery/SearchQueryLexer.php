<?php

/*
 * Generated from resources/grammar/SearchQuery.g4 by ANTLR 4.13.1
 */

namespace App\Parser\SearchQuery {
    use Antlr\Antlr4\Runtime\Atn\ATN;
    use Antlr\Antlr4\Runtime\Atn\ATNDeserializer;
    use Antlr\Antlr4\Runtime\Atn\LexerATNSimulator;
    use Antlr\Antlr4\Runtime\CharStream;
    use Antlr\Antlr4\Runtime\Dfa\DFA;
    use Antlr\Antlr4\Runtime\Lexer;
    use Antlr\Antlr4\Runtime\PredictionContexts\PredictionContextCache;
    use Antlr\Antlr4\Runtime\RuleContext;
    use Antlr\Antlr4\Runtime\RuntimeMetaData;
    use Antlr\Antlr4\Runtime\Vocabulary;
    use Antlr\Antlr4\Runtime\VocabularyImpl;

    final class SearchQueryLexer extends Lexer
    {
        public const WORD = 1, QUOTED_TEXT = 2, NOT = 3, DELIMITER = 4, WS = 5;

        /**
         * @var array<string>
         */
        public const CHANNEL_NAMES = [
            'DEFAULT_TOKEN_CHANNEL', 'HIDDEN'
        ];

        /**
         * @var array<string>
         */
        public const MODE_NAMES = [
            'DEFAULT_MODE'
        ];

        /**
         * @var array<string>
         */
        public const RULE_NAMES = [
            'WORD', 'QUOTED_TEXT', 'NOT', 'DELIMITER', 'WS'
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
            [4, 0, 5, 38, 6, -1, 2, 0, 7, 0, 2, 1, 7, 1, 2, 2, 7, 2, 2, 3, 7, 3,
            2, 4, 7, 4, 1, 0, 1, 0, 5, 0, 14, 8, 0, 10, 0, 12, 0, 17, 9, 0, 1,
            1, 1, 1, 1, 1, 1, 1, 5, 1, 23, 8, 1, 10, 1, 12, 1, 26, 9, 1, 1, 1,
            1, 1, 1, 2, 1, 2, 1, 3, 1, 3, 1, 4, 4, 4, 35, 8, 4, 11, 4, 12, 4,
            36, 0, 0, 5, 1, 1, 3, 2, 5, 3, 7, 4, 9, 5, 1, 0, 4, 6, 0, 9, 10, 13,
            13, 32, 32, 34, 34, 45, 45, 58, 58, 5, 0, 9, 10, 13, 13, 32, 32, 34,
            34, 58, 58, 1, 0, 34, 34, 3, 0, 9, 10, 13, 13, 32, 32, 41, 0, 1, 1,
            0, 0, 0, 0, 3, 1, 0, 0, 0, 0, 5, 1, 0, 0, 0, 0, 7, 1, 0, 0, 0, 0,
            9, 1, 0, 0, 0, 1, 11, 1, 0, 0, 0, 3, 18, 1, 0, 0, 0, 5, 29, 1, 0,
            0, 0, 7, 31, 1, 0, 0, 0, 9, 34, 1, 0, 0, 0, 11, 15, 8, 0, 0, 0, 12,
            14, 8, 1, 0, 0, 13, 12, 1, 0, 0, 0, 14, 17, 1, 0, 0, 0, 15, 13, 1,
            0, 0, 0, 15, 16, 1, 0, 0, 0, 16, 2, 1, 0, 0, 0, 17, 15, 1, 0, 0, 0,
            18, 24, 5, 34, 0, 0, 19, 20, 5, 34, 0, 0, 20, 23, 5, 34, 0, 0, 21,
            23, 8, 2, 0, 0, 22, 19, 1, 0, 0, 0, 22, 21, 1, 0, 0, 0, 23, 26, 1,
            0, 0, 0, 24, 22, 1, 0, 0, 0, 24, 25, 1, 0, 0, 0, 25, 27, 1, 0, 0,
            0, 26, 24, 1, 0, 0, 0, 27, 28, 5, 34, 0, 0, 28, 4, 1, 0, 0, 0, 29,
            30, 5, 45, 0, 0, 30, 6, 1, 0, 0, 0, 31, 32, 5, 58, 0, 0, 32, 8, 1,
            0, 0, 0, 33, 35, 7, 3, 0, 0, 34, 33, 1, 0, 0, 0, 35, 36, 1, 0, 0,
            0, 36, 34, 1, 0, 0, 0, 36, 37, 1, 0, 0, 0, 37, 10, 1, 0, 0, 0, 5,
            0, 15, 22, 24, 36, 0];
        protected static $atn;
        protected static $decisionToDFA;
        protected static $sharedContextCache;

        public function __construct(CharStream $input)
        {
            parent::__construct($input);

            self::initialize();

            $this->interp = new LexerATNSimulator($this, self::$atn, self::$decisionToDFA, self::$sharedContextCache);
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

        public static function vocabulary(): Vocabulary
        {
            static $vocabulary;

            return $vocabulary = $vocabulary ?? new VocabularyImpl(self::LITERAL_NAMES, self::SYMBOLIC_NAMES);
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

        /**
         * @return array<string>
         */
        public function getChannelNames(): array
        {
            return self::CHANNEL_NAMES;
        }

        /**
         * @return array<string>
         */
        public function getModeNames(): array
        {
            return self::MODE_NAMES;
        }

        public function getATN(): ATN
        {
            return self::$atn;
        }

        public function getVocabulary(): Vocabulary
        {
            return self::vocabulary();
        }
    }
}
