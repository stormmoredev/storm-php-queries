<?php

namespace data\models\mapperTypeTest;

use DateTime;

class OrderWithTypedProps
{
    public int $id;

    public DateTime $date;

    public ShipperWithTypedProps $shipper;

    public array $products;
}