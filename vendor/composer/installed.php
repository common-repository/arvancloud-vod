<?php return array(
    'root' => array(
        'name' => 'khorshid/arvancloud-vod-for-wordpress',
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'reference' => '9b50aadd95061ccb1068113645cf1cc6792405ec',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'khorshid/arvancloud-vod-for-wordpress' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '9b50aadd95061ccb1068113645cf1cc6792405ec',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'khorshid/wp-encrypt' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '67cc7ded5f0319845a917ff7dcaa36349512cac8',
            'type' => 'library',
            'install_path' => __DIR__ . '/../khorshid/wp-encrypt',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'woocommerce/action-scheduler' => array(
            'pretty_version' => '3.8.1',
            'version' => '3.8.1.0',
            'reference' => 'e331b534d7de10402d7545a0de50177b874c0779',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../woocommerce/action-scheduler',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'wpbp/debug' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'reference' => '42dcbe2ab429df037f1248da568e970f9021934f',
            'type' => 'library',
            'install_path' => __DIR__ . '/../wpbp/debug',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => true,
        ),
    ),
);
