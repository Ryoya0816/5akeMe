<?php

protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $e)
{
    if ($request->expectsJson() || $request->is('api/*')) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
    return redirect()->guest(url('/'));
}
