<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Flex\Tests\Configurator;

require_once __DIR__.'/TmpDirMock.php';

use Symfony\Flex\Configurator\FrameworkConfigurator;
use PHPUnit\Framework\TestCase;
use Symfony\Flex\Options;

class FrameworkConfiguratorTest extends TestCase
{
    public function testConfigure()
    {

        $recipe = $this->getMockBuilder('Symfony\Flex\Recipe')->disableOriginalConstructor()->getMock();
        $config = sys_get_temp_dir().'/config/packages/framework.yaml';
        if (!file_exists(dirname($config))) {
            mkdir(dirname($config), 0777, true);
        }

        file_put_contents($config, <<<EOF
framework:
    secret: '%env(APP_SECRET)%'
    #default_locale: en

    # uncomment this entire section to enable assets
    ##assets: ~
    
    # uncomment this entire section to enable sessions
    ##session:
    ##    # With this config, PHP's native session handling is used
    ##    handler_id: ~

EOF
        );
        $configurator = new FrameworkConfigurator(
            $this->getMockBuilder('Composer\Composer')->getMock(),
            $this->getMockBuilder('Composer\IO\IOInterface')->getMock(),
            new Options(['config-dir' => dirname(dirname($config))])
        );
        $configurator->configure($recipe, ['session', 'secret', 'default_locale']);
        $this->assertEquals(<<<EOF
framework:
    secret: '%env(APP_SECRET)%'
    #default_locale: en

    # uncomment this entire section to enable assets
    ##assets: ~
    
    # uncomment this entire section to enable sessions
    session:
        # With this config, PHP's native session handling is used
        handler_id: ~

EOF
        , file_get_contents($config));

        $configurator->unconfigure($recipe, ['session', 'secret', 'default_locale']);
        $this->assertEquals(<<<EOF
framework:
    secret: '%env(APP_SECRET)%'
    #default_locale: en

    # uncomment this entire section to enable assets
    ##assets: ~
    
    # uncomment this entire section to enable sessions
    session:
        # With this config, PHP's native session handling is used
        handler_id: ~

EOF
        , file_get_contents($config));

        unlink($config);
    }
}
