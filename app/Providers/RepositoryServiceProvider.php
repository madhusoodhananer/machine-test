<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\HotelRepositoryInterface;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Repositories\Eloquent\EloquentBookingRepository;
use App\Repositories\Eloquent\EloquentHotelRepository;
use App\Repositories\Eloquent\EloquentRoomRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Map repository contracts to their Eloquent implementations.
     *
     * @var array<class-string, class-string>
     */
    private const BINDINGS = [
        HotelRepositoryInterface::class => EloquentHotelRepository::class,
        RoomRepositoryInterface::class => EloquentRoomRepository::class,
        BookingRepositoryInterface::class => EloquentBookingRepository::class,
    ];

    public function register(): void
    {
        foreach (self::BINDINGS as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }
}
