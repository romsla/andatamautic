<?php
$parameters = array(
        'db_driver' => 'pdo_mysql',
        'install_source' => 'Docker',
        'db_host' => 'mauticdb',
        'db_name' => 'mautic',
        'db_user' => 'root',
        'db_password' => 'mysecret',
        'default_timezone' => 'UTC',
        'db_table_prefix' => null,
        'db_port' => '3306',
        'db_backup_tables' => 0,
        'db_backup_prefix' => 'bak_',
        'db_server_version' => '5.7.29-32',
        'mailer_from_name' => 'test test',
        'mailer_from_email' => 'test@test.test',
        'mailer_transport' => 'mail',
        'mailer_host' => null,
        'mailer_port' => null,
        'mailer_user' => null,
        'mailer_password' => null,
        'mailer_encryption' => null,
        'mailer_auth_mode' => null,
        'mailer_spool_type' => 'memory',
        'mailer_spool_path' => '%kernel.root_dir%/spool',
        'secret_key' => '4b5a5706486963dffa6e638dde093a453edb10af74c62e77d5ee212ea2334e42',
        'site_url' => 'http://localhost:8080',
);