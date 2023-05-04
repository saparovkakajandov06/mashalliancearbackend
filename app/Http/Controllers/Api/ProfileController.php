<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProfileUpdateRequset;
use App\Http\Resources\ShowBrandEditResource;
use App\Http\Resources\ShowProfileResource;
use App\Http\Resources\ShowUserResource;
use App\Models\User;
use App\Models\UserImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends BaseController
{

    /**
     * @OA\Get(
     *    path="/profile",
     *    operationId="index",
     *    tags={"Auth::user"},
     *    summary="Auth User Detail",
     *    description="Auth User Detail",
     *    @OA\Parameter(name="id", in="query", description="ID", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="username", in="query", description="Username", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="first_name", in="query", description="first name of user type", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="last_name", in="query", description="last name of user type", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="avatar", in="query", description="Avatar of user", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */

    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            return $this->sendResponse(new ShowProfileResource($user));
        }
        return $this->sendError('Пользователь не найден');
    }


    /**
     * @OA\Get(
     *    path="/user/{id}",
     *    operationId="show",
     *    tags={"user"},
     *    summary="User Detail",
     *    description="User Detail",
     *    @OA\Parameter(name="id", in="query", description="ID", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="username", in="query", description="Username", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="first_name", in="query", description="first name of user type", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="last_name", in="query", description="last name of user type", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="avatar", in="query", description="Avatar of user", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $user = User::find($id);
        if ($user) {
            return $this->sendResponse(new ShowUserResource($user));
        }
        return $this->sendError('Пользователь не найден');
    }


    /**
     * @OA\Get(
     *    path="/profile/edit",
     *    operationId="edit",
     *    tags={"Auth::user"},
     *    summary="Get User for Edit",
     *    description="Get User for Edit",
     *    @OA\Parameter(name="id", in="path", description="Id of User", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */

    public function edit(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return $this->sendError('Вы можете редактировать только свой профиль');
        }
        
        $response = $user;
        
        return $this->sendResponse(new ShowUserResource($response));
        
    }


    /**
     * @OA\Patch(
     *     path="/profile/update",
     *     operationId="update",
     *     tags={"Auth::user"},
     *     summary="Update user in DB",
     *     description="Update user in DB",
     *     @OA\Parameter(name="id", in="path", description="Id of User", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *           required={"username", "first_name", "last_name", "last_name"},
     *            @OA\Property(property="gender_id", type="integer", format="integer", example="Gender type"),
     *            @OA\Property(property="login", type="string", format="string", example="Login"),
     *        ),
     *     ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return $this->sendError('Вы можете редактировать только свой профиль');
        }
        if (!$user->update($request->except('password'))) {
            return $this->sendError('Не удалось обновить данные!');
        }
        
        return $this->sendResponse(new ShowUserResource($user), 'Данные успешно обновлены!');
    }

    /**
     * @OA\Post(
     *      path="/profile/update-avatar",
     *      operationId="updateAvatar",
     *      tags={"Auth::user updateAvatar"},
     *      summary="Update User avatar",
     *      description="Update User avatar",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"avatar"},
     *            @OA\Property(property="avatar",  type="string", format="string", example="user photo"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        $file = $request->file('image');
        
        if (!$file) {
            return $this->sendError($request->all(), 'Нет изображения');
        }
        $destinationPath = "images/avatars";
        $filename = uniqid(time()) . '.' . $file->extension();
        
        Storage::disk('public')
            ->putFileAs($destinationPath, $file, $filename);
        $src = 'avatars/' . $filename;
        $user->update([
            'avatar' => $src,
        ]);
        return $this->sendResponse(new ShowUserResource($user), 'Изображение успешно обновлено');
    }
}
