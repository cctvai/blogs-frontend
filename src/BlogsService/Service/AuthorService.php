<?php
declare(strict_types = 1);

namespace App\BlogsService\Service;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\AuthorRepository;
use BBC\ProgrammesCachingLibrary\CacheInterface;

class AuthorService
{
    /** @var CacheInterface */
    protected $cache;

    /** @var AuthorRepository */
    protected $repository;

    /** @var  IsiteFeedResponseHandler */
    protected $responseHandler;

    public function __construct(
        AuthorRepository $repository,
        IsiteFeedResponseHandler $responseHandler,
        CacheInterface $cache
    ) {
        $this->repository = $repository;
        $this->responseHandler = $responseHandler;
        $this->cache = $cache;
    }

    public function getAuthorByGUID(
        GUID $guid,
        Blog $blog,
        bool $preview,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): ?Author {
        $blogId = $blog->getId();
        $guidString =  (string) $guid;
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $guidString, $blogId, $preview, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($guidString, $blogId, $preview) {
                $response = $this->repository->getAuthorByGUID($blogId, $guidString, $preview);
                $result = $this->responseHandler->getIsiteResult($response);

                return $result->getDomainModels()[0] ?? null;
            },
            [],
            $nullTtl
        );
    }

    public function getAuthorsByLetter(
        Blog $blog,
        string $letter,
        int $page = 1,
        int $limit = 20,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): IsiteResult {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $letter, $page, $limit, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $letter, $page, $limit) {
                $response = $this->repository->getAuthorsByLetter($blog->getId(), $letter, $page, $limit);
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }

    public function getAuthorsByBlog(
        Blog $blog,
        int $page = 1,
        int $limit = 20,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): IsiteResult {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $page, $limit, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $page, $limit) {
                $response = $this->repository->getAuthorsByBlog($blog->getId(), $page, $limit);

                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }
}
