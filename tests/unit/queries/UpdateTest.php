<?php

namespace unit\queries;

use DateTime;
use PHPUnit\Framework\TestCase;
use Storm\Query\IConnection;
use Storm\Query\Queries\UpdateQuery;

final class UpdateTest extends TestCase
{
    private UpdateQuery $update;

    public function testUpdateSql(): void
    {
        $query = $this->update
            ->update('Users')
            ->setValues([
                'name' => 'John Doe',
                'email' => 'john@doe.com',
                'age' => 32,
                'birthday' => new DateTime('1970-01-01')
            ])
            ->where('id', '=', 7);

        $this->assertEquals("UPDATE Users SET name = ?, email = ?, age = ?, birthday = ? WHERE id = ?", $query->getSQL());
    }

    public function testUpdateParameters(): void
    {
        $query = $this->update
            ->update('Users')
            ->setValues([
                'name' => 'John Doe',
                'email' => 'john@doe.com',
                'age' => 32,
                'birthday' => new DateTime('1970-01-01')
            ])
            ->where('id', '=', 7);

        $this->assertEquals(["John Doe", "john@doe.com", 32, "1970-01-01 00:00:00", 7], $query->getParameters());
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->update = new UpdateQuery($mock);
    }
}