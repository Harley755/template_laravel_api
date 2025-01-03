<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Response;
use App\Http\Resources\RoleListResource;
use App\Http\Resources\RoleShowResource;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\ManageRoleRequest;
use App\Http\Requests\Role\SearchRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\PermissionListResource;

class RoleController extends Controller
{
    public function index()
    {
        $this->checkGate('role_access');

        return RoleListResource::collection(Role::orderByDesc('created_at')->get());
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());
        $role->permissions()->sync($request->input('permissions', []));

        return (new RoleShowResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function search(SearchRoleRequest $request)
    {
        $roles = Role::orderByDesc('created_at');

        $title = $request->title;
        $alias = $request->alias;
        $description = $request->description;
        $per_page = $request->per_page ?? 10;

        if ($title) {
            $roles = $roles->where('title', 'LIKE', '%' . $title . '%');
        }

        if ($alias) {
            $roles = $roles->where('alias', 'LIKE', '%' . $alias . '%');
        }

        if ($description) {
            $roles = $roles->where('description', 'LIKE', '%' . $description . '%');
        }

        return RoleListResource::collection($roles->paginate($per_page));
    }

    public function show(Role $role)
    {
        $this->checkGate('role_show');

        return new RoleShowResource($role->load(['permissions']));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return (new RoleShowResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Role $role)
    {
        $this->checkGate('role_delete');

        $role->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function permission_manage(ManageRoleRequest $request)
    {
        $role = Role::find($request->role_id);

        $action = $request->action;
        $permission_ids = $request->permission_ids;
        $permissions = $role->permissions()->whereIn('id', $permission_ids)->get();

        switch ($action) {
            case Permission::ACTION_GRANT:
                foreach ($permission_ids as $permission_id) {
                    $already_attached = $role->permissions()->where('id', $permission_id)->first();
                    if ($already_attached != null) {
                        $already_attached->pivot->update(['is_active' => true]);
                    } else {
                        $role->permissions()->attach($permission_id);
                    }
                }
                break;
            case Permission::ACTION_REVOKE:
                $role->permissions()->detach($permission_ids);
                break;

            case Permission::ACTION_ACTIVATE:
                foreach ($permissions as $permission) {
                    $permission->pivot->update(['is_active' => true]);
                }
                break;
            case Permission::ACTION_DESACTIVATE:
                foreach ($permissions as $permission) {
                    $permission->pivot->update(['is_active' => false]);
                }
                break;

            default:

                break;
        }
        return (PermissionListResource::collection($role->permissions->sortBy('created_at')))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
