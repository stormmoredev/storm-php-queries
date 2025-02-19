./vendor/bin/phpunit --display-deprecations --display-warnings --bootstrap tests\mysql.php tests
./vendor/bin/phpunit --display-deprecations --display-warnings --bootstrap tests\mariadb.php tests
./vendor/bin/phpunit --display-deprecations --display-warnings --bootstrap tests\postgres.php tests
./vendor/bin/phpunit --display-deprecations --display-warnings --bootstrap tests\sqlite.php tests
./vendor/bin/phpunit --display-deprecations --display-warnings --bootstrap tests\sqlserver.php tests