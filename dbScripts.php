<?php
$UpdatesSqlStatments = array(
    '0.0.123' => "ALTER TABLE `users` ADD UNIQUE `Email` (`email`(50));
    DROP TABLE `tbl_log`;
    ALTER TABLE `profiles` 
    ADD COLUMN `admin` TINYINT(1) NULL AFTER `name`,
    ADD COLUMN `super-admin` TINYINT(1) NULL AFTER `admin`,
    CHANGE COLUMN `Name` `name` VARCHAR(50) NOT NULL ;
    ",
    // '0.0.2' => "ALTER TABLE `users` ADD UNIQUE `Email` (`email`(50));"
);
