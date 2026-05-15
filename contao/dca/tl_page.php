<?php

declare(strict_types=1);

/*
 * Adds the "error_410" page type (HTTP 410 Gone) to the core tl_page DCA,
 * modeled after the built-in "error_404" page type.
 */

$GLOBALS['TL_DCA']['tl_page']['palettes']['error_410'] =
    '{title_legend},title,type;{meta_legend},pageTitle,robots,description;{layout_legend:hide},includeLayout;{cache_legend:hide},includeCache;{chmod_legend:hide},includeChmod;{expert_legend:hide},cssClass;{publish_legend},published,start,stop';
