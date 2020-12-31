<?php
// test database! Important not to run tests on production or development databases
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . getenv('MYSQL_DB_HOST_TEST') . ';dbname=' . getenv('MYSQL_DB_NAME'),
    'username' => getenv('MYSQL_DB_USER'),
    'password' => getenv('MYSQL_DB_PASSWORD'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
