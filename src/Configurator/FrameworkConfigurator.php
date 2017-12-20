<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Flex\Configurator;

use Symfony\Flex\Recipe;

/**
 * @author Andrey Mindubaev <andrey@mindubaev.ru>
 */
class FrameworkConfigurator extends AbstractConfigurator
{
    public function configure(Recipe $recipe, $modules)
    {
        $this->write('Configure framework services');

        $target = $this->options->expandTargetDir('%CONFIG_DIR%/packages/framework.yaml');
        $matches = array();
        $lines = [];
        $originLines = file($target);
        for ($i=0; $i<sizeof($originLines); $i++) {
            $line = $originLines[$i];

            $indent = null;
            $found = null;
            foreach ($modules as $module) {
                if (preg_match_all('/^(\s+)(##'.$module.')(\:.*)/s', $line, $matches)) {
                    $indent = $matches[1][0];
                    $found = $module;
                    $lines[] = $indent . $module . $matches[3][0];
                    break;
                }
            }
            if (is_null($found)) {
                $lines[] = $line;
            } else {
                $search = $indent . "## ";
                while (isset($originLines[$i+1]) && strpos($originLines[$i+1], $search) === 0) {
                    $i++;
                    $lines[] = $indent . " " . substr($originLines[$i], strlen($indent) + 3);
                }
            }
        }

        file_put_contents($target, implode('', $lines));
    }

    public function unconfigure(Recipe $recipe, $parameters)
    {

    }
}
