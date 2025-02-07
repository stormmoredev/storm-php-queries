<?php

namespace unit\queries;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\Queries\InsertQuery;

final class InsertTest extends TestCase
{
    private InsertQuery $insert;

    public function testInsertSql(): void
    {
        $query = $this->insert
            ->into('Users')
            ->setRecord([
                'name' => 'John Doe',
                'email' => 'john@doe.com',
                'age' => 32,
                'birthday' => new DateTime('1970-01-01')
            ]);

        $this->assertEquals("INSERT INTO Users (name, email, age, birthday) VALUES (?,?,?,?)", $query->getSQL());
    }

    public function testInsertParameters(): void
    {
        $query = $this->insert
            ->into('Users')
            ->setRecord([
                'name' => 'John Doe',
                'email' => 'john@doe.com',
                'age' => 32,
                'birthday' => new DateTime('1970-01-01')
            ]);

        $this->assertEquals(["John Doe", "john@doe.com", 32, "1970-01-01 00:00:00"], $query->getParameters());
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->insert = new InsertQuery($mock);
    }
}