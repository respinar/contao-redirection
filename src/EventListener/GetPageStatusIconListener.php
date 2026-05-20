<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirects Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\RedirectsBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('getPageStatusIcon')]
class GetPageStatusIconListener
{
    public function __invoke(object $page, string $image): string
    {
        if ('error_410' !== ($page->type ?? null)) {
            return $image;
        }

        $base = 'bundles/respinarredirects/icons/error_410';

        // Unpublished → use the _1 variant
        if (empty($page->published)) {
            return $base.'_1.svg';
        }

        return $base.'.svg';
    }
}
