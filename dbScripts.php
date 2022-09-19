<?php
$UpdatesSqlStatments = array(
    '0.0.0' => "ALTER TABLE `users` ADD UNIQUE `Email` (`email`(50));",
    '0.0.110' => "ALTER TABLE `users` ADD UNIQUE `Email` (`email`(50));",
    '0.0.111' => "CREATE TABLE `tbl_order` (
        `data` json NOT NULL
       ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
       
       CREATE TABLE `tbl_log` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `Head` text COLLATE utf8_unicode_ci,
        `Body` text COLLATE utf8_unicode_ci,
        `Comment` text COLLATE utf8_unicode_ci,
        PRIMARY KEY (`id`)
       ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
    '0.0.122' => "ALTER TABLE `users` ADD UNIQUE `Email` (`email`(50));",
   // '0.0.2' => "ALTER TABLE `users` ADD UNIQUE `Email` (`email`(50));"
);
