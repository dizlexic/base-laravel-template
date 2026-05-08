<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * App-level Permission model so we can apply the project-wide UUID rule
 * (see AGENTS.md §6.3) without forking the Spatie package.
 *
 * The matching migration (database/migrations/..._create_permission_tables.php)
 * declares `permissions.id` as a UUID column; HasUuids generates the value
 * on insert.
 */
class Permission extends SpatiePermission
{
    use HasUuids;
}
