<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirects Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\RedirectsBundle\Security\Voter\DataContainer;

use Contao\CoreBundle\Security\DataContainer\CreateAction;
use Contao\CoreBundle\Security\DataContainer\DeleteAction;
use Contao\CoreBundle\Security\DataContainer\ReadAction;
use Contao\CoreBundle\Security\DataContainer\UpdateAction;
use Contao\CoreBundle\Security\Voter\DataContainer\AbstractDataContainerVoter;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Allow only one "410 Gone" page (error_410) per site root.
 *
 * Mirrors Contao's handling of the built-in 401/403/404 error pages (see
 * PageTypeAccessVoter): as soon as a root page has a 410 page, the option is
 * removed from the page type dropdown and creating/edit a second one is denied.
 * Deleting the page lifts the restriction, so the option becomes available again.
 */
final class GonePageTypeAccessVoter extends AbstractDataContainerVoter
{
    private const PAGE_TYPE = 'error_410';

    public function __construct(private readonly Connection $connection)
    {
    }

    protected function getTable(): string
    {
        return 'tl_page';
    }

    protected function hasAccess(TokenInterface $token, CreateAction|DeleteAction|ReadAction|UpdateAction $action): bool
    {
        // Reads and deletes are always allowed (deleting frees the option again)
        if ($action instanceof ReadAction || $action instanceof DeleteAction) {
            return true;
        }

        // Allow unless the page is being created/changed to an error_410 page
        if (
            (!$action instanceof UpdateAction || self::PAGE_TYPE !== ($action->getCurrent()['type'] ?? null))
            && self::PAGE_TYPE !== ($action->getNew()['type'] ?? null)
        ) {
            return true;
        }

        // A new page needs a parent
        if ($action instanceof CreateAction && null === $action->getNewPid()) {
            return false;
        }

        $type = $action->getNew()['type'] ?? ($action instanceof UpdateAction ? $action->getCurrent()['type'] : null);
        $currentPid = $action instanceof UpdateAction ? (int) $action->getCurrentPid() : null;
        $pid = (int) ($action->getNewPid() ?? $currentPid);

        // Always allow copying to the clipboard
        if (
            $action instanceof CreateAction
            && self::PAGE_TYPE === $type
            && \array_key_exists('sorting', (array) $action->getNew())
            && null === $action->getNew()['sorting']
        ) {
            return true;
        }

        if (
            (null !== $action->getNewPid() || null !== ($action->getNew()['sorting'] ?? null))
            && (!$action instanceof UpdateAction || self::PAGE_TYPE === $type)
            && ($pid !== $currentPid)
            && (!$this->isRootPage($pid) || $this->hasGonePageInRoot($pid))
        ) {
            return false;
        }

        return !(
            $action instanceof UpdateAction
            && self::PAGE_TYPE === ($action->getNew()['type'] ?? null)
            && (
                !$this->isRootPage((int) $action->getCurrentPid())
                || $this->hasGonePageInRoot((int) $action->getCurrentPid())
            )
        );
    }

    /**
     * @return bool True if the given page id belongs to a root page
     */
    private function isRootPage(int $pageId): bool
    {
        return 'root' === $this->connection->fetchOne(
            'SELECT type FROM tl_page WHERE id = ?',
            [$pageId],
        );
    }

    /**
     * @return bool True if the given root page already has an error_410 page as a direct child
     */
    private function hasGonePageInRoot(int $rootId): bool
    {
        return (bool) $this->connection->fetchOne(
            'SELECT COUNT(*)
             FROM tl_page p
             JOIN tl_page r ON p.pid = r.id
             WHERE r.type = ? AND r.id = ? AND p.type = ?',
            ['root', $rootId, self::PAGE_TYPE],
        );
    }
}
