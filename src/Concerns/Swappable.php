<?php

namespace Laravie\Dhosa\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\FactoryBuilder;
use Illuminate\Database\Eloquent\Model;
use Laravie\Dhosa\HotSwap;

trait Swappable
{
    /**
     * Make Hot-swappable model.
     *
     * @param  array  $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function hs(array $attributes = []): Model
    {
        return HotSwap::make(static::hsAliasName(), $attributes) ?? new static($attributes);
    }

    /**
     * Make Hot-swappable model.
     *
     * @param  array  $attributes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function hsQuery(): Builder
    {
        return static::hs()->newQuery();
    }

    /**
     * Make Hot-swappable faker model.
     *
     * @param  array  $attributes
     *
     * @return \Illuminate\Database\Eloquent\FactoryBuilder
     */
    public static function hsFaker(): FactoryBuilder
    {
        $arguments = \func_get_args();

        \array_unshift($arguments, static::hsFinder());

        return \factory(...$arguments);
    }

    /**
     * Make Hot-swappable model on write connection.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function hsOnWriteConnection(): Builder
    {
        return static::hs()->query()->onWritePdo();
    }

    /**
     * Make Hot-swappable model on specific connection.
     *
     * @param  string|null  $connection
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function hsOnConnection(?string $connection = null): Builder
    {
        return \tap(static::hs(), static function ($instance) use ($connection) {
            $instance->setConnection($connection);
        })->newQuery();
    }

    /**
     * Find Hot-swappable full namespace model.
     *
     * @return string
     */
    public static function hsFinder(): string
    {
        return HotSwap::eloquent(static::hsAliasName()) ?? static::class;
    }

    /**
     * Get Hot-swappable alias name.
     *
     * @return string
     */
    abstract public static function hsAliasName(): string;
}
