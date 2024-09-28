<?php

declare(strict_types=1);

namespace AspirePress\Cdn\Data\Entities;

use AspirePress\Cdn\Data\Enums\AsString;
use AspirePress\Cdn\Data\Values\Version;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

final class Plugin
{
    private function __construct(
        private UuidInterface $id,
        private string $name,
        private string $slug,
        private Version $currentVersion,
        private ?DownloadableFile $file
    )
    {
    }

    public static function fromArray(array $data): self
    {
        Assert::keyExists($data, 'id');
        Assert::keyExists($data, 'name');
        Assert::keyExists($data, 'slug');
        Assert::keyExists($data, 'current_version');
        Assert::keyExists($data, 'file');

        Assert::uuid($data['id']);
        Assert::string($data['name']);
        Assert::string($data['slug']);
        Assert::string($data['current_version']);
        Assert::isInstanceOf($data['file'], DownloadableFile::class);

        return new self(
            Uuid::fromString($data['id']),
            $data['name'],
            $data['slug'],
            Version::fromString($data['current_version']),
            $data['file']
        );
    }

    public static function fromValues(UuidInterface $id, string $name, string $slug, Version $currentVersion, ?DownloadableFile $file = null): self
    {
        Assert::notEmpty($name);
        Assert::notEmpty($slug);

        return new self(
            $id,
            $name,
            $slug,
            $currentVersion,
            $file
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCurrentVersion(): Version
    {
        return $this->currentVersion;
    }

    public function getFile(): ?DownloadableFile
    {
        return $this->file;
    }

    public function newerVersionAvailable(Version|string $version): bool
    {
        return $this->currentVersion->versionNewerThan($version);
    }

    public function toArray(AsString $asString = AsString::NO): array
    {
        $id = ($asString == AsString::YES) ? (string) $this->id : $this->id;
        $version = ($asString == AsString::YES) ? (string) $this->currentVersion : $this->currentVersion;
        $file = ($asString == AsString::YES && $this->getFile()) ? $this->getFile()->toArray(true) : $this->getFile();

        return [
            'id' => $id,
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'current_version' => $version,
            'file' => $file,
        ];
    }
}
