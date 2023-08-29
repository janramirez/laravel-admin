<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Resources\PermissionResource;

class PermissionController
{
    public function index()
    {
        return PermissionResource::collection(Permission::all());
    }
}
