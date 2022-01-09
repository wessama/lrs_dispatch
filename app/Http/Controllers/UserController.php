<?php

namespace App\Http\Controllers;

use App\Accounts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/*
 * @group User management
 *
 * Manage authenticated users.
 */
class UserController extends Controller
{
    /**
     * Controller constructor.
     *
     * @param  \App\Accounts  $accounts
     */
    public function __construct(Accounts $accounts)
    {
        $this->accounts = $accounts;
    }

    /**
     * Get all users.
     *
     * List all currently registered user accounts.
     * @authenticated
     * @group User management
     *
     * @response 200 {
     * "data": [
     * {
     * "id": 1,
     * "name": "Name",
     * "email": "email@example.com"
     * }
     * ],
     * "meta": {
     * "pagination": {
     * "total": 1,
     * "count": 1,
     * "per_page": 20,
     * "current_page": 1,
     * "total_pages": 1,
     * "links": {}
     * }
     * }
     * }
     *
     * @response 401 {
     *   "error":
     *   {
     *      "message": "Unauthorized",
     *      "status": 401,
     *   }
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->accounts->getUsers($request);

        return response()->json($users, Response::HTTP_OK);
    }

    /**
     * Store a user.
     * @hideFromAPIDocumentation
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $this->accounts->storeUser($request->all());

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * Get a user.
     * @hideFromAPIDocumentation
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->accounts->getUserById($id);

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Update a user.
     * @hideFromAPIDocumentation
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->accounts->updateUserById($id, $request->all());

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Delete a user.
     * @hideFromAPIDocumentation
     * @param  int  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->accounts->deleteUserById($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
