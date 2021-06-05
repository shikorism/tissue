grammar SearchQuery;

query
    : expression (WS expression)* EOF
    ;

expression
    : negativeExpression
    | positiveExpression
    ;

negativeExpression
    : NOT positiveExpression
    ;

positiveExpression
    : target keyword
    | keyword
    ;

target
    : WORD DELIMITER
    ;

keyword
    : WORD (DELIMITER WORD)*
    | WORD DELIMITER
    | QUOTED_TEXT
    ;

WORD
    : ~[-": \t\r\n] ~[": \t\r\n]*
    ;

QUOTED_TEXT
    : '"' ('""' | ~'"')* '"'
    ;

NOT
    : '-'
    ;

DELIMITER
    : ':'
    ;

WS
    : [\t\r\n ]+
    ;

