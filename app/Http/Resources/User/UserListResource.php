<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="UserListResource",
 *     description="User resource",
 *     @OA\Xml(
 *         name="UserListResource"
 *     )
 * )
 */
class UserListResource extends JsonResource
{
    /**
     * @OA\Property(
     *     title="Data",
     *     description="Data wrapper",
     *     type="array",
     *     @OA\Items(
     *         type="object",
     *         @OA\Property(
     *             property="id",
     *             type="integer",
     *             description="ID"
     *         ),
     *         @OA\Property(
     *             property="firstname",
     *             type="string",
     *             description="First Name"
     *         ),
     *         @OA\Property(
     *             property="lastname",
     *             type="string",
     *             description="Last Name"
     *         ),
     *         @OA\Property(
     *             property="fullname",
     *             type="string",
     *             description="Full Name"
     *         ),
     *         @OA\Property(
     *             property="email",
     *             type="string",
     *             format="email",
     *             description="Email"
     *         ),
     *         @OA\Property(
     *             property="phone_number",
     *             type="string",
     *             description="Phone Number"
     *         ),
     *         @OA\Property(
     *             property="roles",
     *             type="array",
     *             @OA\Items(
     *                 type="string"
     *             ),
     *             description="Roles"
     *         ),
     *         @OA\Property(
     *             property="avatar",
     *             type="string",
     *             description="Avatar URL"
     *         ),
     *         @OA\Property(
     *             property="created_at",
     *             type="string",
     *             format="date-time",
     *             description="Created At"
     *         )
     *     )
     * )
     *
     * @var array
     */
    private $data;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'roles' => RoleResource::collection($this->roles),
            'avatar' => $this->avatar,
            'created_at' => $this->created_at?->format(config('panel.datetime_format')),
        ];
    }
}
