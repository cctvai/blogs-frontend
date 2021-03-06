<?php
declare(strict_types = 1);

namespace Tests\App\Builders;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Module\ModuleInterface;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\ValueObject\Social;
use Faker\Factory;

class BlogBuilder implements BuilderInterface
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $shortSynopsis;

    /** @var string */
    private $description;

    /** @var Image */
    private $image;

    /** @var bool */
    private $showImageInDescription;

    /** @var string */
    private $language;

    /** @var Social */
    private $social;

    /** @var string */
    private $commentsApiKey;

    /** @var string */
    private $bbcSite;

    /** @var string */
    private $brandingId;

    /** @var Post */
    private $featuredPost;

    /** @var ModuleInterface[] */
    private $modules;

    /** @var bool */
    private $isArchived;

    public function build()
    {
        $faker = Factory::create();
        /** @var string $name */
        $name = $this->name ?? $faker->words(3, true);
        /** @var string $description */
        $description = $this->description ?? $faker->sentences(2, true);
        /** @var string $words */
        $words = $faker->words(3, true);

        return new Blog(
            $this->id ?? $faker->word,
            $name,
            $this->shortSynopsis ?? $faker->sentence(),
            $description,
            $this->showImageInDescription ?? $faker->boolean,
            $this->language ?? 'en-GB',
            $this->bbcSite ?? $faker->word,
            $this->brandingId ?? $faker->numerify('br-#####'),
            $this->modules ?? [new FreeText($words, $faker->sentence())],
            $this->social ?? new Social($faker->lexify('@????????'), $faker->word, $faker->word),
            !is_null($this->commentsApiKey) ? $this->commentsApiKey : $faker->word,
            $this->featuredPost,
            $this->image ?? new Image($faker->regexify('[0-9b-df-hj-np-tv-z]{8,15}')),
            $this->isArchived ?? false
        );
    }

    public function withId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    public function withName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function withShortSynopsis(string $shortSynopsis)
    {
        $this->shortSynopsis = $shortSynopsis;
        return $this;
    }

    public function withDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function withImage(Image $image)
    {
        $this->image = $image;
        return $this;
    }

    public function withShowImageInDescription(bool $showImage)
    {
        $this->showImageInDescription = $showImage;
        return $this;
    }

    public function withLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }

    public function withSocial(Social $social)
    {
        $this->social = $social;
        return $this;
    }

    public function withCommentsApiKey(string $commentsApiKey)
    {
        $this->commentsApiKey = $commentsApiKey;
        return $this;
    }

    public function withBbcSite(string $bbcSite)
    {
        $this->bbcSite = $bbcSite;
        return $this;
    }

    public function withBrandingId(string $brandingId)
    {
        $this->brandingId = $brandingId;
        return $this;
    }

    public function withFeaturedPost(Post $featuredPost)
    {
        $this->featuredPost = $featuredPost;
        return $this;
    }

    public function withModules(array $modules)
    {
        $this->modules = $modules;
        return $this;
    }

    public function withIsArchived(bool $isArchived)
    {
        $this->isArchived = $isArchived;
        return $this;
    }

    public static function default()
    {
        return new self();
    }
}
