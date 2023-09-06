<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserCreateRequest;
use App\Jobs\AdminAdded;
use App\Models\UserRole;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class UserController
{
    /**
     *  @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    
    public function index(Request $request) 
    {
        $this->userService->allows('view', 'users');
        
        return $this->userService->all($request->input('page', 1));
    }

    public function show($id) 
    {
        $this->userService->allows('view', 'users');

        $user = $this->userService->get($id);

        return new UserResource($user);
    }


    public function store(UserCreateRequest $request)
    {
        $this->userService->allows('edit', 'users');

        $data = $request->only('first_name', 'last_name', 'email')
            + ['password' => 1234];

        $user = $this->userService->create($data);

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id'),
        ]);

        AdminAdded::dispatch($user->email);

        return response(new UserResource($user), HttpFoundationResponse::HTTP_CREATED);
    }


    public function update(Request $request, $id)  
    {
        $this->userService->allows('edit', 'users');

        $user = $this->userService->update($id, $request->only('first_name', 'last_name', 'email'));

        UserRole::where('user_id', $user->id)->delete();

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id'),
        ]);

        return response(new UserResource($user), 202);
    }

    
    public function destroy($id) 
    {
        $this->userService->allows('edit', 'users');

        $this->userService->delete($id);
        
        return response(null, HttpFoundationResponse::HTTP_NO_CONTENT);
    }

}
