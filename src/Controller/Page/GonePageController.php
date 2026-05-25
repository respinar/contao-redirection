<?php

declare(strict_types=1);

/*
 * This file is part of Contao Redirects Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\RedirectsBundle\Controller\Page;

use Contao\CoreBundle\Controller\Page\AbstractPageController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsPage;
use Contao\CoreBundle\Routing\Page\ContentCompositionInterface;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders the "gone" (410) page. Modeled after Contao's error page controller,
 * but for the HTTP 410 Gone status. The page is created in the page tree under a
 * root page and is never reachable via a URL of its own; it is only rendered when
 * a redirection rule with status code 410 matches.
 */
#[AsPage('error_410', path: false)]
class GonePageController extends AbstractPageController implements ContentCompositionInterface
{
    public function __invoke(PageModel $pageModel): Response
    {
        return $this->renderPage($pageModel)->setStatusCode(Response::HTTP_GONE);
    }

    public function supportsContentComposition(PageModel $pageModel): bool
    {
        return true;
    }

    protected function setCacheHeaders(Response $response, PageModel $pageModel): Response
    {
        // Never cache the 410 page
        $response->headers->set('Cache-Control', 'no-cache, no-store');

        return $response->setPrivate();
    }
}
