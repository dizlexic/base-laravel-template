<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * App-level Role model so we can apply the project-wide UUID rule
 * (see AGENTS.md §6.3) without forking the Spatie package.
 *
 * The matching migration (database/migrations/..._create_permission_tables.php)
 * declares `roles.id` as a UUID column; HasUuids generates the value on insert.
 */
class Role extends SpatieRole
{
    use HasUuids;
}
