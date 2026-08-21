<?php
// app/Auth/StaffUserProvider.php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class StaffUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $row = DB::selectOne('SELECT * FROM public.auth_get_staff_by_id(?)', [$identifier]);

        return $row ? $this->hydrate($row) : null;
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials['email'])) {
            return null;
        }

        $row = DB::selectOne('SELECT * FROM public.auth_get_staff_by_email(?)', [$credentials['email']]);

        return $row ? $this->hydrate($row) : null;
    }

    public function retrieveByToken($identifier, $token)
    {
        $row = DB::selectOne(
            'SELECT * FROM public.auth_get_staff_by_remember_token(?, ?)',
            [$identifier, $token]
        );

        return $row ? $this->hydrate($row) : null;
    }

    protected function hydrate(object $row): Authenticatable
    {
        return $this->createModel()->newFromBuilder((array) $row);
    }
}
