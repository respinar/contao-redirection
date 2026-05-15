<?php
// src/EventListener/RedirectionListener.php
namespace Respinar\RedirectionBundle\EventListener;

use Contao\CoreBundle\InsertTag\InsertTagParser;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContext;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 1000)]
final class RedirectionListener
{
    private Connection $db;
    private InsertTagParser $insertTagParser;
    private RequestContext $requestContext;

    public function __construct(Connection $db, InsertTagParser $insertTagParser, RequestContext $requestContext)
    {
        $this->db = $db;
        $this->insertTagParser = $insertTagParser;
        $this->requestContext = $requestContext;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $uri = substr($request->getPathInfo(), 1); // Strip leading / (e.g., /test -> test)

        try {
            $redirect = $this->db->fetchAssociative(
                'SELECT target_url, status_code FROM tl_redirection WHERE source_url = ? AND active = ?',
                [$uri, '1']
            );

            if (is_array($redirect)) {
                $statusCode = (int) ($redirect['status_code'] ?? 301);

                if ($statusCode === 410) {
                    // Return 410 Gone without redirecting
                    $response = new Response('This resource is permanently gone.', 410);
                    $event->setResponse($response);
                } elseif (in_array($statusCode, [301, 302])) {
                    // Resolve target_url, including insert tags
                    $targetUrl = $this->generateTargetUrl($redirect['target_url']);

                    // Ensure proper URL handling
                    if (!preg_match('#^https?://#i', $targetUrl)) {
                        // If not absolute, make it absolute using RequestContext
                        $targetUrl = $this->requestContext->getScheme() . '://' . $this->requestContext->getHost() . '/' . ltrim($targetUrl, '/');
                    }

                    $response = new RedirectResponse($targetUrl, $statusCode);
                    $event->setResponse($response);
                }
                // Ignore other status codes
            }
        } catch (\Exception $e) {
            // Optionally log: $this->logger->error(...) if logger is added
        }
    }

    /**
     * Resolve insert tags in target_url
     */
    private function generateTargetUrl(string $target): string
    {
        if (!str_contains($target, '{{')) {
            return $target;
        }

        // Ensure link insert tags are absolute
        $target = preg_replace('/{{(link(?:_[^:]+)?::[^|}]+)}}/i', '{{$1|absolute}}', $target);

        return $this->insertTagParser->replace($target);
    }
}
