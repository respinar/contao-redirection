<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirection Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\RedirectionBundle\Model;

use Contao\Model;
use Contao\Model\Collection;

class RedirectionModel extends Model
{
    protected static $strTable = 'tl_redirection';


    /**
     * Find published redirection items.
     *
     * @return Collection<RedirectionModel>|null A collection of models or null if there are no redirections
     */
    public static function findPublished(): Collection|null
    {
        $t = static::$strTable;
        $arrColumns = ["$t.published=1"];

        return static::findBy($arrColumns);
    }
}
