<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\Php73\Rector\FuncCall\SetCookieRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return static function (RectorConfig $rectorConfig): void {
    // Ensure file system caching is used instead of in-memory.
    $rectorConfig->cacheClass(FileCacheStorage::class);
    $rectorConfig->cacheDirectory('./build/cache/rector');

    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // Define what rule sets will be applied
    $rectorConfig->import(LevelSetList::UP_TO_PHP_80);
    $rectorConfig->import(SetList::CODE_QUALITY);
    $rectorConfig->import(SetList::DEAD_CODE);
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_100);
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_CODE_QUALITY,);
};