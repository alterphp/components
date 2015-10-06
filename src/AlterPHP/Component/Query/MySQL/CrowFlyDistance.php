<?php

namespace AlterPHP\Component\Query\MySQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class CrowFlyDistance extends FunctionNode
{
    public $firstLatExpression = null;
    public $firstLngExpression = null;
    public $secondLatExpression = null;
    public $secondLngExpression = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstLatExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->firstLngExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondLatExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondLngExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return
            '(
                (
                    (
                        acos(
                            sin('.$this->firstLatExpression->dispatch($sqlWalker).' * pi() / 180)
                            * sin('.$this->secondLatExpression->dispatch($sqlWalker).' * pi() / 180)
                            +
                            cos('.$this->firstLatExpression->dispatch($sqlWalker).' * pi() / 180)
                            * cos('.$this->secondLatExpression->dispatch($sqlWalker).' * pi() / 180)
                            * cos((('.$this->firstLngExpression->dispatch($sqlWalker).' - '.$this->secondLngExpression->dispatch($sqlWalker).') * pi() / 180))
                        )
                    ) * 180 / pi()
                ) * 60 * 1.853159616
            )'
        ;
    }
}
