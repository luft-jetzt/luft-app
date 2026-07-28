<?php declare(strict_types=1);

namespace App\Air\Util\EntityMerger\Attribute;

/**
 * Marks an entity property as safe to overwrite through EntityMerger.
 *
 * EntityMerger uses an explicit allowlist: only properties carrying this
 * attribute are copied from the deserialized request body onto the managed
 * entity. Identity keys (e.g. Station::$stationCode, City::$slug) and system
 * fields must NOT be annotated, so they can never be changed via the update
 * endpoints.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Mergeable
{
}
