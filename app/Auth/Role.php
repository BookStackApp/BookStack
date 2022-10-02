<?php

namespace BookStack\Auth;

use BookStack\Auth\Permissions\JointPermission;
use BookStack\Auth\Permissions\RolePermission;
use BookStack\Interfaces\Loggable;
use BookStack\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Role.
 *
 * @property int        $id
 * @property string     $display_name
 * @property string     $description
 * @property string     $external_auth_id
 * @property string     $system_name
 * @property bool       $mfa_enforced
 * @property Collection $users
 */
class Role extends Model implements Loggable
{
    use HasFactory;

    protected $fillable = ['display_name', 'description', 'external_auth_id'];

    protected $hidden = ['pivot'];

    /**
     * The roles that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->orderBy('name', 'asc');
    }

    /**
     * Get all related JointPermissions.
     */
    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class);
    }

    /**
     * The RolePermissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(RolePermission::class, 'permission_role', 'role_id', 'permission_id');
    }

    /**
     * Check if this role has a permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        $permissions = $this->getRelationValue('permissions');
        foreach ($permissions as $permission) {
            if ($permission->getRawAttribute('name') === $permissionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a permission to this role.
     */
    public function attachPermission(RolePermission $permission)
    {
        $this->permissions()->attach($permission->id);
    }

    /**
     * Detach a single permission from this role.
     */
    public function detachPermission(RolePermission $permission)
    {
        $this->permissions()->detach([$permission->id]);
    }

    /**
     * Get the role of the specified display name.
     */
    public static function getRole(string $displayName): ?self
    {
        return static::query()->where('display_name', '=', $displayName)->first();
    }

    /**
     * Get the role object for the specified system role.
     */
    public static function getSystemRole(string $systemName): ?self
    {
        return static::query()->where('system_name', '=', $systemName)->first();
    }

    /**
     * Get all visible roles.
     */
    public static function visible(): Collection
    {
        return static::query()->where('hidden', '=', false)->orderBy('name')->get();
    }

    /**
     * Get the roles that can be restricted.
     */
    public static function restrictable(): Collection
    {
        return static::query()
            ->where('system_name', '!=', 'admin')
            ->orderBy('display_name', 'asc')
            ->get();
    }

    /**
     * Get a role to represent the case of 'Everyone else' in the system.
     * Used within the interface since the default-fallback for permissions uses role_id=0.
     */
    public static function getEveryoneElseRole(): self
    {
        return (new static())->forceFill([
            'id' => 0,
            'display_name' => 'Everyone Else',
            'description'  => 'Set permissions for all roles not specifically overridden.'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function logDescriptor(): string
    {
        return "({$this->id}) {$this->display_name}";
    }
}
