<?php

namespace data\models;

class Customer
{
    public int $id;
    public string $name;
    public string $address;
    public string $city;
    public string $postalCode;
    public string $country;
    public array $orders;
}