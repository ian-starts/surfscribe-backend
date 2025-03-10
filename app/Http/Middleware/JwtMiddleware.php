<?php


namespace App\Http\Middleware;


use App\Factories\CamelCaseJsonResponseFactory;
use App\User;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Closure;
use Exception;
use Illuminate\Http\Request;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        try {
            $token = explode('Bearer ', $request->header('authorization'))[1];
        } catch (Exception $exception) {
            return (new CamelCaseJsonResponseFactory())->json(
                [
                    'error' => 'Token not provided.'
                ],
                401
            );
        }

        if (!$token) {
            // Unauthorized response if token not there
            return (new CamelCaseJsonResponseFactory)->json(
                [
                    'error' => 'Token not provided.'
                ],
                401
            );
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch (ExpiredException $e) {
            return (new CamelCaseJsonResponseFactory)->json(
                [
                    'error' => 'Provided token is expired.'
                ],
                400
            );
        } catch (Exception $e) {
            return (new CamelCaseJsonResponseFactory)->json(
                [
                    'error' => 'An error while decoding token.'
                ],
                400
            );
        }
        $user = User::find($credentials->sub);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}
