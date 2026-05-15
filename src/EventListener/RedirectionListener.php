<?php

declare(strict_types=1);

namespace Respinar\RedirectionBundle\EventListener;

use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Routing\PageFinder;
use Contao\CoreBundle\Routing\Page\PageRegistry;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Handles URL redirects (301/302) and 410 Gone responses on the front end.
 *
 * The listener only acts on main, front-end GET/HEAD requests so that the
 * backend, the install tool, the profiler and fragment routes are never matched
 * against user-defined redirect rules.
 */
#[AsEventListener(event: KernelEvents::REQUEST, priority: 1000)]
final class RedirectionListener
{
    /**
     * Route name prefixes that must never be subject to redirect rules.
     */
    private const BACKEND_ROUTE_PREFIXES = [
        'contao_backend',
        'contao_install',
        'contao_maintenance',
    ];

    public function __construct(
        private readonly Connection $db,
        private readonly InsertTagParser $insertTagParser,
        private readonly LoggerInterface $logger,
        private readonly PageFinder $pageFinder,
        private readonly PageRegistry $pageRegistry,
        private readonly HttpKernelInterface $httpKernel,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Only handle safe (GET/HEAD) front-end requests.
        if (!$request->isMethodSafe()) {
            return;
        }

        $route = (string) $request->attributes->get('_route', '');

        foreach (self::BACKEND_ROUTE_PREFIXES as $prefix) {
            if (str_starts_with($route, $prefix)) {
                return;
            }
        }

        // Never redirect within the Contao install/back end file paths.
        if (preg_match('#^/(contao|_profiler|_wdt|_fragment|_error)(/|$)#', $request->getPathInfo())) {
            return;
        }

        // Normalize the path: strip the leading slash and any query string.
        $path = $request->getPathInfo();
        $uri = ltrim($path, '/');

        if ('' === $uri) {
            return;
        }

        try {
            $rows = $this->db->fetchAllAssociative(
                'SELECT id, source_url, match_type, target_url, status_code
                 FROM tl_redirection
                 WHERE active = ?',
                ['1'],
            );
        } catch (\Throwable $e) {
            $this->logger->error('Redirection lookup failed.', ['exception' => $e]);

            return;
        }

        foreach ($rows as $row) {
            $matches = [];

            $matchType = $row['match_type'] ?? 'exact';

            if ('wildcard' === $matchType) {
                $pattern = '#^'.str_replace('#', '\#', $row['source_url']).'$#i';

                if (!preg_match($pattern, $uri, $matches)) {
                    continue;
                }
            } else {
                if ($row['source_url'] !== $uri) {
                    continue;
                }

                $matches = [];
            }

            $this->applyResponse($event, $row, $matches);

            return;
        }
    }

    private function applyResponse(RequestEvent $event, array $row, array $matches): void
    {
        $statusCode = (int) ($row['status_code'] ?? 301);

        if (410 === $statusCode) {
            $event->setResponse($this->renderGoneResponse($event->getRequest()));

            return;
        }

        if (!\in_array($statusCode, [301, 302, 303, 307, 308], true)) {
            return;
        }

        $request = $event->getRequest();

        // Substitute wildcard subpatterns ($1, $2, ...) into the target.
        $targetUrl = preg_replace_callback(
            '/\$(\d+)/',
            static fn (array $m): string => $matches[(int) $m[1]] ?? '',
            $row['target_url'],
        );

        // Resolve insert tags (e.g. {{link_url::4}}, {{link::4}}).
        $targetUrl = $this->insertTagParser->replace($targetUrl);

        // Build an absolute URL from a relative destination.
        if (!$this->isAbsoluteUrl($targetUrl)) {
            $targetUrl = $request->getSchemeAndHttpHost().$request->getBasePath().'/'.ltrim($targetUrl, '/');
        }

        $event->setResponse(new RedirectResponse($targetUrl, $statusCode));
    }

    private function isAbsoluteUrl(string $url): bool
    {
        return (bool) preg_match('#^[a-z][a-z0-9+.-]*://#i', $url)
            || str_starts_with($url, '//')
            || str_starts_with($url, 'mailto:')
            || str_starts_with($url, 'tel:');
    }

    /**
     * Renders the configured "error_410" page for the current site with a 410
     * status code. Falls back to a plain 410 response if no such page exists.
     */
    private function renderGoneResponse(Request $request): Response
    {
        try {
            $page = $this->pageFinder->findFirstPageOfTypeForRequest($request, 'error_410');
        } catch (\Throwable $e) {
            $this->logger->warning('Unable to locate an error_410 page.', ['exception' => $e]);

            return new Response('This resource is permanently gone.', Response::HTTP_GONE);
        }

        if (!$page) {
            return new Response('This resource is permanently gone.', Response::HTTP_GONE);
        }

        $route = $this->pageRegistry->getRoute($page);
        $subRequest = $request->duplicate(null, null, $route->getDefaults());

        try {
            $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
        } catch (ResponseException $e) {
            $response = $e->getResponse();
        } catch (\Throwable $e) {
            $this->logger->error('Failed to render the error_410 page.', ['exception' => $e]);

            return new Response('This resource is permanently gone.', Response::HTTP_GONE);
        }

        return $response->setStatusCode(Response::HTTP_GONE);
    }
}
