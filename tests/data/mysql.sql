CREATE TABLE insert_test
(
    id   integer auto_increment primary key,
    name varchar(64) not null UNIQUE
);

CREATE TABLE update_test
(
    id   integer auto_increment primary key,
    name varchar(64) not null UNIQUE
);

CREATE TABLE delete_test
(
    id   integer auto_increment primary key,
    name varchar(64) not null UNIQUE
);

CREATE TABLE types_test
(
    id       integer auto_increment primary key,
    uid      char(36)      null,
    name     varchar(64)   not null UNIQUE,
    num      int           null,
    num_f    decimal(5, 2) null,
    is_set   boolean       null,
    date     date          null,
    datetime timestamp     null
);

INSERT INTO types_test (uid, name, num, num_f, is_set, date, datetime) VALUES ('c3a5d2f8-471d-47bc-a56c-6f73d61215b9', 'first',1, 1.11, true, '2021-01-01', '2021-01-01 12:13:14');
INSERT INTO types_test (uid, name, num, num_f, is_set, date, datetime) VALUES ('3f333df6-90a4-4fda-8dd3-9485d27cee36', 'second',2, 2.22, false, '2022-02-02', '2022-02-02 12:13:14');

CREATE TABLE categories
(
    category_id   int NOT NULL,
    category_name varchar(255) DEFAULT NULL,
    description   varchar(255) DEFAULT NULL
);

CREATE TABLE customers
(
    customer_id   int NOT NULL,
    customer_name varchar(255) DEFAULT NULL,
    contact_name  varchar(255) DEFAULT NULL,
    address       varchar(255) DEFAULT NULL,
    city          varchar(255) DEFAULT NULL,
    postal_code   varchar(255) DEFAULT NULL,
    country       varchar(255) DEFAULT NULL
);

CREATE TABLE tags
(
    tag_id int NOT NULL,
    name varchar(32)
);

CREATE TABLE products
(
    product_id   int NOT NULL,
    product_name varchar(255)  DEFAULT NULL,
    supplier_id  int           DEFAULT NULL,
    category_id  int           DEFAULT NULL,
    unit         varchar(255)  DEFAULT NULL,
    price        decimal(5, 2) DEFAULT NULL
);

CREATE TABLE products_tags
(
    product_id int NOT NULL,
    tag_id int NOT NULL
);

CREATE TABLE employees
(
    employee_id int NOT NULL,
    last_name   varchar(255) DEFAULT NULL,
    first_name  varchar(255) DEFAULT NULL,
    birth_date  date         DEFAULT NULL,
    photo       varchar(255) DEFAULT NULL,
    notes       text
);

CREATE TABLE orders
(
    order_id    int NOT NULL,
    customer_id int  DEFAULT NULL,
    employee_id int  DEFAULT NULL,
    order_date  date DEFAULT NULL,
    shipper_id  int  DEFAULT NULL
);


CREATE TABLE order_details
(
    order_detail_id int NOT NULL,
    order_id        int DEFAULT NULL,
    product_id      int DEFAULT NULL,
    quantity        int DEFAULT NULL
);

CREATE TABLE shippers
(
    shipper_id   int NOT NULL,
    shipper_name varchar(255) DEFAULT NULL,
    phone        varchar(255) DEFAULT NULL
);

CREATE TABLE shippers_addresses
(
    shipper_address_id int NOT NULL,
    shipper_id         int NOT NULL,
    address            varchar(255) DEFAULT NULL
);

CREATE TABLE suppliers
(
    supplier_id   int NOT NULL,
    supplier_name varchar(255) DEFAULT NULL,
    contact_name  varchar(255) DEFAULT NULL,
    address       varchar(255) DEFAULT NULL,
    city          varchar(255) DEFAULT NULL,
    postal_code   varchar(255) DEFAULT NULL,
    country       varchar(255) DEFAULT NULL,
    phone         varchar(255) DEFAULT NULL
);