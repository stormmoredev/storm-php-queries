<?php

namespace integration;

use data\ConnectionProvider;
use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Stormmore\Queries\StormQueries;

final class TypesTest extends TestCase
{
    public function testReadString(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $item = $queries
            ->select('types_test')
            ->where('id', 1)
            ->find();

        $this->assertEquals("first", $item->name);
    }

    public function testReadBoolWithTrueValue(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $item = $queries
            ->select('types_test')
            ->where('id', 1)
            ->find();

        $this->assertEquals(true, $item->is_set);
    }

    public function testReadBoolWithFalseValue(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $item = $queries
            ->select('types_test')
            ->where('id', 1)
            ->find();

        $this->assertEquals(true, $item->is_set);
    }

    public function testReadInteger(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $item = $queries
            ->select('types_test')
            ->where('id', 2)
            ->find();

        $this->assertEquals(2, $item->num);
    }

    public function testReadDecimal(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $item = $queries
            ->select('types_test')
            ->where('id', 2)
            ->find();

        $this->assertEquals(2.22, $item->num_f);
    }

    public function testReadDate(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $item = $queries
            ->select('types_test')
            ->where('id', 2)
            ->find();

        $this->assertEquals('2022-02-02', $item->date);
    }

    public function testReadDateTime(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $item = $queries
            ->select('types_test', '*')
            ->where('id', 2)
            ->find();

        $this->assertEquals(new DateTime('2022-02-02 12:13:14'), new DateTime($item->datetime));
    }

    public function testReadUid(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $item = $queries
            ->select('types_test')
            ->where('id', 2)
            ->find();

        $this->assertEquals('3f333df6-90a4-4fda-8dd3-9485d27cee36', strtolower($item->uid));
    }

    public function testWriteReadBoolWithTrueValue(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $id = $queries->insert('types_test', ['name' => 'third', 'is_set' => true]);
        $item = $queries->find('types_test', ['id' => $id]);

        $this->assertEquals(true, $item->is_set);
    }

    public function testWriteReadBoolWithFalseValue(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $id = $queries->insert('types_test', ['name' => 'fourth', 'is_set' => false]);
        $item = $queries->find('types_test', ['id' => $id]);

        $this->assertEquals(false, $item->is_set);
    }

    public function testWriteReadInt(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $id = $queries->insert('types_test', ['name' => 'fifth', 'num' => 7]);
        $item = $queries->find('types_test', ['id' => $id]);

        $this->assertEquals(7, $item->num);
    }

    public function testWriteReadDecimal(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $id = $queries->insert('types_test', ['name' => 'sixth', 'num_f' => 7.7]);
        $item = $queries->find('types_test', ['id' => $id]);

        $this->assertEquals(7.7, $item->num_f);
    }

    public function testWriteReadDate(): void
    {
        $queries = ConnectionProvider::getStormQueries();
        $date = new DateTime("2020-02-02");

        $id = $queries->insert('types_test', ['name' => 'seventh', 'date' => $date]);
        $item = $queries->find('types_test', ['id' => $id]);

        $this->assertEquals(new DateTime("2020-02-02"), new DateTime($item->date));
    }

    public function testWriteReadDateTime(): void
    {
        $queries = ConnectionProvider::getStormQueries();
        $date = new DateTime("2020-02-02 10:00:00", new DateTimeZone("Etc/GMT+1"));

        $id = $queries->insert('types_test', ['name' => 'eighth', 'datetime' => $date]);
        $item = $queries->find('types_test', ['id' => $id]);

        $this->assertEquals(new DateTime("2020-02-02 11:00:00"), new DateTime($item->datetime));
    }

    public function testWriteReadUid(): void
    {
        $queries = ConnectionProvider::getStormQueries();

        $id = $queries->insert('types_test', ['name' => 'ninth', 'uid' => '0f7ef9b1-b809-4678-a418-18218cfa75d7']);
        $item = $queries->find('types_test', ['id' => $id]);

        $this->assertEquals('0f7ef9b1-b809-4678-a418-18218cfa75d7', strtolower($item->uid));
    }
}