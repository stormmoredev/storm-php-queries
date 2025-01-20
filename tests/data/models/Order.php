<?php

namespace data\models;

use DateTime;

class Order
{
    public int $id;

    public DateTime $date;

    public Shipper $shipper;

    public array $products;
}