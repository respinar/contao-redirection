<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirects Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\RedirectsBundle\Repository;

use Doctrine\DBAL\Connection;
use Psr\Cache\CacheItemPoolInterface;

final class RedirectsRepository
{
    /**
     * Cache key holding the list of published redirects.
     */
    private const CACHE_KEY = 'tl_redirects.published';

    /**
     * Short TTL as a fallback so the list self-heals even if the cache is not
     * explicitly invalidated (e.g. when a redirect is toggled directly in the list).
     */
    private const CACHE_TTL = 60;

    public function __construct(
        private readonly Connection $connection,
        private readonly CacheItemPoolInterface $cache,
    ) {
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
        $item = $this->cache->getItem(self::CACHE_KEY);

        if (!$item->isHit()) {
            $item->set($this->loadPublished());
            $item->expiresAfter(self::CACHE_TTL);
            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * Invalidates the cache so the next lookup re-reads the database.
     */
    public function clearCache(): void
    {
        $this->cache->deleteItem(self::CACHE_KEY);
    }

    private function loadPublished(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, source_url, wildcard, target_url, status_code
             FROM tl_redirects
             WHERE published = ?',
            [1],
        );
    }
}
