<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirection Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\RedirectsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Respinar\RedirectsBundle\RespinarRedirectsBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(RespinarRedirectsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
