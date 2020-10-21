<?php

namespace Concrete5\BrandCentralConnector;

class AssetDetails
{
    /** @var string */
    protected $title;
    /** @var string */
    protected $description;
    /** @var string */
    protected $thumbnailUrl;
    /** @var array */
    protected $files;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return AssetDetails
     */
    public function setTitle(string $title): AssetDetails
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return AssetDetails
     */
    public function setDescription(string $description): AssetDetails
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    /**
     * @param string $thumbnailUrl
     * @return AssetDetails
     */
    public function setThumbnailUrl(string $thumbnailUrl): AssetDetails
    {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     * @return AssetDetails
     */
    public function setFiles(array $files): AssetDetails
    {
        $this->files = $files;
        return $this;
    }

}