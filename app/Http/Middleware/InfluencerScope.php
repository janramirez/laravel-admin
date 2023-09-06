<?php

namespace App\Http\Middleware;

use App\Services\UserService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class InfluencerScope
{
    /**
     *  @var UserService
     */
    private $userService;

    public function __construct(UserService  $userService)
    {
        $this->userService = $userService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($this->userService->isInfluencer()) {
            return $next($request);
        }

        throw new AuthenticationException();
    }
}
