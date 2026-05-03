<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * All tenant data for the web layer must be scoped to the authenticated user's {@see \App\Models\User::$mandant_id}.
 */
trait ResolvesCurrentMandant
{
    /**
     * Current mandant for the signed-in user. Routes should use the `mandant` middleware so this is always set in the app.
     */
    protected function mandantId(?Request $request = null): int
    {
        $request ??= request();
        $id = $request->user()?->mandant_id;
        abort_if($id === null, 403);

        return (int) $id;
    }
}
