<?php return array(
    'root' => array(
        'name' => 'facebook/pixel-for-wordpress',
        'pretty_version' => 'dev-default',
        'version' => 'dev-default',
        'reference' => NULL,
        'type' => 'project',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'facebook/php-business-sdk' => array(
            'pretty_version' => '16.0.1',
            'version' => '16.0.1.0',
            'reference' => 'ce3e5d19dcb03c079567c3d9b66503180378a38a',
            'type' => 'library',
            'install_path' => __DIR__ . '/../facebook/php-business-sdk',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'facebook/pixel-for-wordpress' => array(
            'pretty_version' => 'dev-default',
            'version' => 'dev-default',
            'reference' => NULL,
            'type' => 'project',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'techcrunch/wp-async-task' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '9bdbbf9df4ff5179711bb58b9a2451296f6753dc',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../techcrunch/wp-async-task',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
    ),
);
