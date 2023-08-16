<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Facade\FlareClient\Http\Response;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class UserController extends Controller
{
    public function index() 
    {
        Gate::authorize('view', 'users');
        
        $users = User::paginate();

        return UserResource::collection($users);
    }

    public function show($id) 
    {
        Gate::authorize('view', 'users');

        $user = User::find($id);

        return new UserResource($user);
    }


    public function store(UserCreateRequest $request)
    {
        Gate::authorize('edit', 'users');

        $user = User::create(
            $request->only('first_name', 'last_name', 'email','role_id')
            + ['password' => Hash::make(1234)]
        );

        return response(new UserResource($user), HttpFoundationResponse::HTTP_CREATED);
    }


    public function update(Request $request, $id)  
    {
        Gate::authorize('edit', 'users');

        $user = User::find($id);

        $user->update($request->only('first_name', 'last_name', 'email', 'role_id'));

        return response(new UserResource($user), 202);
    }

    
    public function destroy($id) 
    {
        Gate::authorize('edit', 'users');

        User::destroy($id);
        
        return response(null, HttpFoundationResponse::HTTP_NO_CONTENT);
    }

    public function user()
    {
        $user = Auth::user();

        return (new UserResource($user))->additional([
            'data' => [
                'permissions' => $user->permissions()
            ]
        ]);
    }

    public function updateInfo(UpdateInfoRequest $request)
    {
        $user = Auth::user();

        $user->update($request->only('first_name', 'last_name', 'email'));

        return response(new UserResource($user), HttpFoundationResponse::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'password'=>Hash::make($request->input('password'))
        ]);

        return response(new UserResource($user), HttpFoundationResponse::HTTP_ACCEPTED);
    }
}
