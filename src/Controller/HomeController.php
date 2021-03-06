<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\BlogService;

class HomeController extends BaseController
{
    public function __invoke(BlogService $blogService)
    {
        $blogResult = $blogService->getAllBlogs();
        $blogs = $blogResult->getDomainModels();

        $blogsByLetter = $this->getBlogsByLetter($blogs);

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('blogs-index', 'index-home');
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata('The latest blogs from the BBC');

        return $this->renderWithBrandingAndOrbit(
            'home/show.html.twig',
            $pageMetadata,
            $analyticsLabels,
            $this->brandingHelper()->requestBranding(''), // Default branding
            [
                'hasBlogs' => !empty($blogsByLetter),
                'blogsByLetter' => $blogsByLetter,
            ]
        );
    }

    private function removePrefix($prefix, $str): string
    {
        return trim(preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $str));
    }

    /**
     * @param Blog[] $blogs
     * @return Blog[][]
     */
    private function getBlogsByLetter(array $blogs): array
    {
        $blogsByLetter = [];

        /** @var Blog $blog */
        foreach ($blogs as $blog) {
            if ($blog->getIsArchived()) {
                continue;
            }
            $name = $blog->getName();
            $name = $this->removePrefix('BBC ', $name);
            $name = $this->removePrefix('Blog ', $name);
            $initalLetter = substr($name, 0, 1);
            if (!array_key_exists($initalLetter, $blogsByLetter)) {
                $blogsByLetter[$initalLetter] = [];
            }
            $blogsByLetter[$initalLetter][] = $blog;
        }
        ksort($blogsByLetter);

        return $blogsByLetter;
    }
}
