<?php

declare(strict_types=1);

namespace App\Model\InternalApi\Folder;

use App\Enum\FolderEnum;
use App\Model\InternalApi\InternalResponse;

class GetFoldersResponse extends InternalResponse
{
    protected array $resource;

    public function getResource(): array
    {
        return $this->resource;
    }

    public function setResource(array $resource): GetFoldersResponse
    {
        $this->resource = $resource;

        return $this;
    }

    public function getFolders(): array
    {
        return $this->resource[FolderEnum::FOLDERS];
    }

    public function getMetaTotal(): int
    {
        return $this->resource[FolderEnum::META][FolderEnum::TOTAL];
    }
}
