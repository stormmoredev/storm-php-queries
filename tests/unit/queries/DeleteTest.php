<?php

namespace unit\queries;

use PHPUnit\Framework\TestCase;
use Stormmore\Queries\IConnection;
use Stormmore\Queries\Queries\DeleteQuery;

final class DeleteTest extends TestCase
{
    private DeleteQuery $delete;

    public function testDeleteSql(): void
    {
        $query = $this->delete
            ->from('Users')
            ->where('id', '=', 7);

        $this->assertEquals("DELETE FROM Users WHERE id = ?", $query->getSQL());
    }

    public function testDeleteParameters(): void
    {
        $query = $this->delete
            ->from('Users')
            ->where('id', '=', 7);

        $this->assertEquals([7], $query->getParameters());
    }

    protected function setUp(): void
    {
        $mock = $this->createMock(IConnection::class);
        $this->delete = new DeleteQuery($mock);
    }
}