<?php
namespace App\Domain\Models\Traits;

use App\Observers\AuditObserver;

trait AuditObservable
{
    public static function bootAuditObservable(): void
    {
        self::observe(AuditObserver::class);
    }
}
