<?php
$stderr = fopen('php://stderr', 'w');

fwrite($stderr, "\nWriting initial Mautic config\n");

$parameters = array(
	'db_driver'      => 'pdo_mysql',
	'install_source' => 'Docker'
);

if(array_key_exists('MAUTIC_DB_HOST', $_ENV)) {
    // Figure out if we have a port in the database host string
    if (strpos($_ENV['MAUTIC_DB_HOST'], ':') !== false) {
        list($host, $port) = explode(':', $_ENV['MAUTIC_DB_HOST'], 2);
        $parameters['db_port'] = $port;
    }
    else {
        $host = $_ENV['MAUTIC_DB_HOST'];
    }
    $parameters['db_host'] = $host;
}
if(array_key_exists('MAUTIC_DB_NAME', $_ENV)) {
    $parameters['db_name'] = $_ENV['MAUTIC_DB_NAME'];
}
if(array_key_exists('MAUTIC_DB_TABLE_PREFIX', $_ENV)) {
    $parameters['db_table_prefix'] = $_ENV['MAUTIC_DB_TABLE_PREFIX'];
}
if(array_key_exists('MAUTIC_DB_USER', $_ENV)) {
    $parameters['db_user'] = $_ENV['MAUTIC_DB_USER'];
}
if(array_key_exists('MAUTIC_DB_PASSWORD', $_ENV)) {
    $parameters['db_password'] = $_ENV['MAUTIC_DB_PASSWORD'];
}
if(array_key_exists('MAUTIC_TRUSTED_PROXIES', $_ENV)) {
    $proxies = explode(',', $_ENV['MAUTIC_TRUSTED_PROXIES']);
    $parameters['trusted_proxies'] = $proxies;
}

if(array_key_exists('MAUTIC_SITE_URL', $_ENV)) {
    $parameters['site_url'] = $_ENV['MAUTIC_SITE_URL'];
}

if(array_key_exists('MAUTIC_MAILER_FROM_NAME', $_ENV)) {
    $parameters['mailer_from_name'] = $_ENV['MAUTIC_MAILER_FROM_NAME'];
}

if(array_key_exists('MAUTIC_MAILER_FROM_EMAIL', $_ENV)) {
    $parameters['mailer_from_email'] = $_ENV['MAUTIC_MAILER_FROM_EMAIL'];
}

if(array_key_exists('MAUTIC_MAILER_HOST', $_ENV)) {
    $parameters['mailer_host'] = $_ENV['MAUTIC_MAILER_HOST'];
}
$parameters['mailer_transport'] = 'smtp';
$parameters['mailer_port'] = '6025';

if(array_key_exists('PHP_INI_DATE_TIMEZONE', $_ENV)) {
    $parameters['default_timezone'] = $_ENV['PHP_INI_DATE_TIMEZONE'];
}

$path     = '/var/www/html/app/config/local.php';
$rendered = "<?php\n\$parameters = ".var_export($parameters, true).";\n";

$status = file_put_contents($path, $rendered);

if ($status === false) {
	fwrite($stderr, "\nCould not write configuration file to $path, you can create this file with the following contents:\n\n$rendered\n");
}

