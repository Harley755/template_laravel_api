<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\SearchUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserListResource;
use App\Http\Resources\User\UserShowResource;


use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/users",
     *      tags={"Users"},
     *      summary="Liste des users",
     *      description="Returns list of users",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserListResource")
     *       ),
     *     )
     */
    public function index(Request $request)
    {
        $this->checkGate('user_access');

        return UserListResource::collection($request->per_page ? User::with(['roles'])->paginate($request->per_page) : User::with(['roles'])->get());
    }

    public function search(SearchUserRequest $request)
    {
        $periode = $request->periode;
        $email = $request->email;
        $phone_number = $request->phone_number;
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $fullname = $request->fullname;
        $role_ids = $request->role_ids;
        $roles = $request->roles;
        $per_page = $request->per_page ?? 10;


        $users = User::query()->with(['roles']);

        if ($periode) {
            $from = new Carbon($periode['from']);
            $to = new Carbon($periode['to']);

            if ($from) {
                $users = $users->where('created_at', '>=', $from);
            }

            if ($to) {
                $users = $users->where('created_at', '<=', $to);
            }
        }

        if ($email) {
            $users = $users->where('email', 'LIKE', '%' . $email . '%');
        }

        if ($phone_number) {
            $users = $users->where('phone_number', 'LIKE', '%' . $phone_number . '%');
        }

        if ($firstname) {
            $users = $users->where('firstname', 'LIKE', '%' . $firstname . '%');
        }

        if ($lastname) {
            $users = $users->where('lastname', 'LIKE', '%' . $lastname . '%');
        }

        if ($fullname) {
            $users = $users->orWhere('lastname', 'LIKE', '%' . $lastname . '%')->where('lastname', 'LIKE', '%' . $lastname . '%');
        }

        if ($roles) {
            $users = $users->whereHas('roles', function ($role_item) use (&$roles) {
                $role_item->whereIn('alias', $roles);
            });
        }

        if ($role_ids) {
            $users = $users->whereHas('roles', function ($role) use (&$role_ids) {
                $role->whereIn('id', $role_ids);
            });
        }

        return UserListResource::collection($users->orderByDesc('created_at')->paginate($per_page));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        if ($request->roles) {
            $roles = Role::whereIn('alias', $request->roles)->pluck('id');

            $user->roles()->syncWithoutDetaching($roles);
        }

        if ($request->avatar) {
            $user->clearMediaCollection(User::AVATAR_COLLECTION_NAME);
            $user->addMedia($request->avatar)->toMediaCollection(User::AVATAR_COLLECTION_NAME);
        }

        //Handle User created actions

        return (new UserListResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }


    /**
     * @OA\Get(
     *      path="/users{user}",
     *      tags={"Users"},
     *      @OA\Parameter(
     *          name="user",
     *          in="path",
     *          description="ID de la resource",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/UserListResource")
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(ref="#/components/schemas/UserListResource")
     *       ),
     *     )
     */
    public function show(User $user)
    {
        $this->checkGate('user_show');

        return new UserShowResource($user->load('roles'));
    }


    /**
     * @OA\Put(
     *      path="/users/{user}",
     *      operationId="updateUser",
     *      tags={"Users"},
     *      summary="Met à jour un utilisateur existant",
     *      description="Met à jour les informations d'un utilisateur ainsi que ses rôles et son avatar.",
     *      @OA\Parameter(
     *          name="user",
     *          in="path",
     *          description="ID de l'utilisateur à mettre à jour",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *         ref="#/components/schemas/UserListResource"
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Utilisateur mis à jour avec succès",
     *          @OA\JsonContent(ref="#/components/schemas/UserListResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Utilisateur non trouvé",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Utilisateur non trouvé"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Erreur de validation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Les données fournies sont invalides."
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  description="Détails des erreurs de validation"
     *              )
     *          )
     *      ),
     *      security={
     *          {"bearerAuth": {}}
     *      }
     * )
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        if ($request->roles) {
            $user->roles()->detach();

            $roles = Role::whereIn('alias', $request->roles)->pluck('id');

            $user->roles()->syncWithoutDetaching($roles);
        }

        if ($request->avatar) {
            $user->clearMediaCollection(User::AVATAR_COLLECTION_NAME);
            $user->addMedia($request->avatar)->toMediaCollection(User::AVATAR_COLLECTION_NAME);
        }

        return (new UserListResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }


    /**
     * @OA\Delete(
     *      path="/users/{user}",
     *      operationId="deleteUser",
     *      tags={"Users"},
     *      summary="Supprime un utilisateur",
     *      description="Supprime un utilisateur existant par son ID.",
     *      @OA\Parameter(
     *          name="user",
     *          in="path",
     *          description="ID de l'utilisateur à supprimer",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Utilisateur supprimé avec succès",
     *          @OA\JsonContent(
     *              type="string",
     *              example="No Content"
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Accès refusé",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Vous n'avez pas les permissions nécessaires pour effectuer cette action."
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Utilisateur non trouvé",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Utilisateur non trouvé"
     *              )
     *          )
     *      ),
     *      security={
     *          {"bearerAuth": {}}
     *      }
     * )
     */
    public function destroy(User $user)
    {
        $this->checkGate('user_delete');

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
