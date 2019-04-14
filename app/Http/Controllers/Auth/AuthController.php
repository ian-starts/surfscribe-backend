<?php


namespace App\Http\Controllers\Auth;

use App\Factories\CamelCaseJsonResponseFactory;
use App\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    /**
     * @param Request $request
     * @param User    $user
     *
     * @return mixed
     */
    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60 * 60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validate(
            $request,
            [
                'email'    => 'required|email',
                'password' => 'required'
            ]
        );
        // Find the user by email
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the
            // below respose for now.
            return (new CamelCaseJsonResponseFactory())->json(
                [
                    'error' => 'Email does not exist.'
                ],
                400
            );
        }
        // Verify the password and generate the token
        if (Hash::check($request->input('password'), $user->password)) {
            return (new CamelCaseJsonResponseFactory)->json(
                [
                    'token' => $this->jwt($user)
                ],
                200
            );
        }
        // Bad Request response
        return (new CamelCaseJsonResponseFactory)->json(
            [
                'error' => 'Email or password is wrong.'
            ],
            400
        );

    }

    public function signup(Request $request)
    {
        $this->validate(
            $request,
            [
                'name'     => 'required',
                'email'    => 'required|email|max:255',
                'password' => 'required',
            ]

        );
        $user = User::create(
            [
                'name'     => $request->get('name'),
                'email'    => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]
        );
        return (new CamelCaseJsonResponseFactory)->json(
            [
                'token' => $this->jwt($user)
            ],
            200
        );
    }
}
