<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirects Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\RedirectsBundle\Model;

use Contao\Model;
use Contao\Model\Collection;

class RedirectsModel extends Model
{
    protected static $strTable = 'tl_redirects';

    /**
     * Find published redirection items.
     *
     * @return Collection<RedirectsModel>|null A collection of models or null if there are no redirects
     */
    public static function findPublished(): Collection|null
    {
        return static::findBy(['published=?'], [1]);
    }
}
