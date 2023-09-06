<?php

namespace App\Models;

class User
{
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $is_influencer;

    public function __construct($json)
    {

        $this->id = $json['id'];
        $this->first_name = $json['first_name'];
        $this->last_name = $json['last_name'];
        $this->email = $json['email'];
        $this->is_influencer = $json['is_influencer'] ?? 0;
    }

    public function role()
    {
        $userRole = UserRole::where('user_id',$this->id)->first();

        return Role::find($userRole->role_id);
    }

    public function permissions()
    {
        return $this->role()->permissions->pluck('name');
    }

    public function hasAccess($access)
    {
        return $this->permissions()->contains($access);
    }

    public function isAdmin(): bool
    {
        return $this->is_influencer === 0;
    }

    public function isInfluencer(): bool
    {
        return $this->is_influencer === 1;
    }

    public function revenue()
    {
        $orders = Order::where('user_id', $this->id)->where('complete', 1)->get();

        return $orders->sum(function (Order $order){
            return $order->influencer_total;
        });
    }

    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
