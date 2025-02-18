<?php


namespace Stormmore\Queries\Sql\Clauses;

use InvalidArgumentException;

class ConditionalClause
{
    private array $tokens = [];

    public function __construct(private string $clauseName)
    {
    }

    public function where(): void
    {
        $arguments = func_get_args();
        $this->add('AND', $arguments);
    }

    public function orWhere(): void
    {
        $arguments = func_get_args();
        $this->add('OR', $arguments);
    }

    public function whereString(string $condition, array $parameters): void
    {
        !empty($condition) or throw new InvalidArgumentException("WHERE has to have at least one parameter");

        $this->addOperator('AND');

        $this->tokens[] = new StringStatement($condition, $parameters);
    }

    public function having(): void
    {
        $arguments = func_get_args();
        $this->add('AND', $arguments);
    }

    public function orHaving(): void
    {
        $arguments = func_get_args();
        $this->add('OR', $arguments);
    }

    public function toString(): string
    {
        $whereClause = '';
        foreach ($this->tokens as $i => $token) {
            if ($token instanceof BoolStatement) {
                $whereClause .= $token->toString();
            }
            else if ($token instanceof StringStatement) {
                $whereClause .= $token->toString();
            }
            else if (is_string($token) and in_array($token, ['OR', 'AND'])) {
                $whereClause .= ' ' . $token .  ' ';
            }
            else if (is_string($token) and in_array($token, ['(', ')'])) {
                $whereClause .= $token;
            }
        }
        if ($whereClause != '') {
            $whereClause = $this->clauseName . " " . $whereClause;
        }
        return $whereClause;
    }

    public function getParameters(): array
    {
        $parameters = [];
        foreach($this->tokens as  $token) {
            if ($token instanceof BoolStatement) {
                $parameters = array_merge($parameters, $token->getParameters());
            }
            if ($token instanceof StringStatement) {
                $parameters = array_merge($parameters, $token->getParameters());
            }
        }
        return $parameters;
    }

    private function add($operator, $arguments): void
    {
        $argsNum = count($arguments);

        $argsNum or throw new InvalidArgumentException("WHERE has to have at least one parameter");

        $this->addOperator($operator);

        if ($argsNum == 1) {
            is_callable($arguments[0]) or throw new InvalidArgumentException("In case of one argument it should be function");
            $this->tokens[] = '(';
            $arguments[0]($this);
            $this->tokens[] = ')';
        }
        else if ($argsNum == 2 and is_string($arguments[0]) and is_array($arguments[1])) {
            $this->tokens[] = new StringStatement($arguments[0], $arguments[1]);
        }
        else if ($argsNum > 1) {
            $this->tokens[] = new BoolStatement($arguments);
        }
    }

    private function addOperator($operator): void
    {
        if (!count($this->tokens)) {
            return;
        }
        $last = $this->tokens[count($this->tokens) - 1];
        if ($last != '(') {
            $this->tokens[] = $operator;
        }
    }
}