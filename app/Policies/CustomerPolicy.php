<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Customer $customer)
    {
        return $user->id === $customer->user_id;
    }

    public function update(User $user, Customer $customer)
    {
        return $user->id === $customer->user_id;
    }

    public function delete(User $user, Customer $customer)
    {
        return $user->id === $customer->user_id;
    }
}