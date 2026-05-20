<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirection Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\RedirectsBundle\Repository;

use Doctrine\DBAL\Connection;

final class RedirectsRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    /**
     * Returns all published redirects.
     *
     * @return array<int, array{
     *     id: int,
     *     source_url: string,
     *     wildcard: bool,
     *     target_url: string,
     *     status_code: int
     * }>
     */
    public function findPublished(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, source_url, wildcard, target_url, status_code
             FROM tl_redirects
             WHERE published = ?',
            [1],
        );
    }
}
