<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\SearchPermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\Permission\PermissionListResource;
use App\Http\Resources\Permission\PermissionShowResource;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $this->checkGate('permission_access');

        return PermissionListResource::collection($request->per_page ? Permission::orderByDesc('created_at')->paginate($request->per_page) : Permission::orderByDesc('created_at')->get());
    }

    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->validated());

        return (new PermissionShowResource($permission))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Permission $permission)
    {
        $this->checkGate('permission_show');

        return new PermissionShowResource($permission);
    }

    public function search(SearchPermissionRequest $request)
    {
        $permissions = Permission::query()->orderByDesc('created_at');

        $only_active = $request->only_active ?? true;

        $per_page = $request->per_page ?? 10;

        $role_id = $request->role_id;

        $module = $request->module;

        $action = $request->action;

        $resource = $request->resource;

        $description = $request->description;


        if ($role_id != null) {
            $permissions = $permissions->whereHas('roles', function ($role) use ($role_id) {
                $role->where('id', $role_id);
            });
        }

        if ($module != null) {
            $permissions = $permissions->where('module', 'LIKE', '%' . $module . '%')
                ->orWhere('title', 'LIKE', '%' . $module . '%')
                ->orWhere('description', 'LIKE', '%' . $module . '%');
        }

        if ($description != null) {
            $permissions = $permissions->where('description', 'LIKE', '%' . $description . '%');
        }

        if ($action != null) {
            $permissions = $permissions->where('action', 'LIKE', '%' . $action . '%');
        }

        if ($resource != null) {
            $permissions = $permissions->where('resource', 'LIKE', '%' . $resource . '%');
        }

        if ($only_active) {
            $permissions = $permissions->where('is_active', true);
        }

        return (PermissionListResource::collection($permissions->orderBy('id')->paginate($per_page)));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->all());

        return (new PermissionShowResource($permission))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Permission $permission)
    {
        $this->checkGate('permission_delete');

        $permission->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
