<?php

namespace Vkaf\Bundle\OafBundle\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class TimestampDiff extends FunctionNode
{
    private $firstExpression;
    private $secondExpression;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'TIMESTAMPDIFF(SECOND, %s, %s)',
            $this->firstExpression->dispatch($sqlWalker),
            $this->secondExpression->dispatch($sqlWalker)
        );
    }
}
