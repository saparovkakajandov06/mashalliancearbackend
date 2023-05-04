<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends BaseController
{

    /**
     * @OA\Post(
     *      path="/register",
     *      operationId="register",
     *      tags={"Users"},
     *      summary="Registration User",
     *      description="Registration User",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"username", "password", "confirm_password"},
     *            @OA\Property(property="username",  type="string", format="string", example="username"),
     *            @OA\Property(property="password", type="string", format="string", example="password"),
     *            @OA\Property(property="confirm_password", type="string", format="string", example="confirm_password"),
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
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        
        $success['token'] = $user->createToken(config('app.name'))->plainTextToken;
        $success['id'] = $user->id;
        $success['username'] = $user->username;
        
        return $this->sendResponse($success, 'User register successfully.');
    }


    /**
     * @OA\Post(
     *      path="/login",
     *      operationId="login",
     *      tags={"Auth::user"},
     *      summary="Login User",
     *      description="Login User",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"username", "password"},
     *            @OA\Property(property="username",  type="string", format="string", example="username"),
     *            @OA\Property(property="password", type="string", format="string", example="password"),
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
    public function login(Request $request)
    {
        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {
            $user = Auth::user();
            $success['token'] = $user->createToken(config('app.name'))->plainTextToken;
            $success['id'] = $user->id;
            $success['username'] = $user->username;
            return $this->sendResponse($success, 'Пользователь успешно авторизован.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    /**
     * @OA\Post(
     *      path="/logout",
     *      operationId="logout",
     *      tags={"Auth::user"},
     *      summary="Logout User",
     *      description="Logout User",
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */

    public function logout()
    {
        if (method_exists(Auth::user()
            ->currentAccessToken(), 'delete')) {
            Auth::user()
                ->currentAccessToken()
                ->delete();
        }
        
        auth()
            ->guard('web')
            ->logout();
        
        return $this->sendResponse([], 'Успешно.');
    }
}
