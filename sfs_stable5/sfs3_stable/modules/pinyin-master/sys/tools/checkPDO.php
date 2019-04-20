<?php
/*
if (!defined('PDO::ATTR_DRIVER_NAME')) {
echo 'PDO unavailable';
}
elseif (defined('PDO::ATTR_DRIVER_NAME')) {
echo 'PDO available';
}
foreach (PDO::getAvailableDrivers() as $driver) {
echo $driver . PHP_EOL;
}
*/
print in_array('sqlite',PDO::getAvailableDrivers());
?>
