<?php

namespace data\models\mapperTypeTest;

use data\models\Shipper;
use DateTime;

class OrderWithTypedInitProps
{
    public int $id;

    public DateTime $date;

    public ShipperWithTypedInitProps $shipper;

    public array $details = array();

    public function __construct()
    {
        $this->shipper = new ShipperWithTypedInitProps();
        $this->date = new DateTime();
    }
}