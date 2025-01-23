<?php

namespace data\models\mapperTypeTest;

class DetailWithTypedInitProps
{
    public int $id = 0;
    public int $quantity = 1;
    public ProductWithTypedInitProps $product;

    public function __construct()
    {
        $this->product = new ProductWithTypedInitProps();
    }
}