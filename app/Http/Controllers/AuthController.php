<?php

namespace App\Http\Controllers;

use App\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Authentication
 *
 * Authenticate users.
 */
class AuthController extends Controller
{
    /**
     * Controller constructor.
     *
     * @param  \App\Auth  $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get a JWT via given credentials.
     *
     * Obtain a bearer token to use for authenticating future requests using an e-mail/password combo.
     *
     * @group Authentication
     *
     * @bodyParam email string required User email
     * @bodyParam password string required Password
     *
     * @response 200 {
     *      "data": {
     *                 "access_token": "{YOUR_AUTH_KEY}",
     *                 "token_type": "bearer",
     *                 "expires_in": 3600
     *              }
     * }
     *
     * @response 401 {
     *   "error":
     *   {
     *      "message": "Unauthorized",
     *      "status": 401,
     *   }
     * }
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $token = $this->auth->authenticateByEmailAndPassword(
            (string) $request->input('email'),
            (string) $request->input('password')
        );

        return response()->json($token, Response::HTTP_OK);
    }

    /**
     * Get the authenticated User.
     * @hideFromAPIDocumentation
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = $this->auth->getAuthenticatedUser();

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Refresh a token.
     * @hideFromAPIDocumentation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(): JsonResponse
    {
        $token = $this->auth->refreshAuthenticationToken();

        return response()->json($token, Response::HTTP_OK);
    }

    /**
     * Log the user out (Invalidate the token).
     * @hideFromAPIDocumentation
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(): JsonResponse
    {
        $this->auth->invalidateAuthenticationToken();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
