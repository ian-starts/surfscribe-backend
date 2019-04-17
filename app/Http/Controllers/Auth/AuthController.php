<?php


namespace App\Http\Controllers\Auth;

use App\Factories\CamelCaseJsonResponseFactory;
use App\Mail\MailNotifications;
use App\Mail\MailPasswordReset;
use App\PasswordReset;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    /**
     * @param Request        $request
     * @param UserRepository $repository
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function checkToken(Request $request, UserRepository $repository)
    {
        $user = $repository->getByTokenAndEmail($request->get('token', null), $request->get('email', null));
        if ($user instanceof User) {
            return response()->json(['success' => true, 'message' => 'User can reset password'], 200);
        }
        return response()->json(
            ['success' => false, 'message' => 'User not authorized for password reset'],
            200
        );
    }

    /**
     * @param Request        $request
     * @param UserRepository $repository
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function requestPasswordReset(Request $request, UserRepository $repository)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|email'
            ]
        );
        $user = User::query()->where('email', '=', $request->get('email'))->firstOrFail();
        $repository->createPasswordReset($user);
        return response()->json(['success' => true, 'message' => 'Reset token requested'], 201);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function resetPassword(Request $request, UserRepository $repository)
    {
        $this->validate(
            $request,
            [
                'email'    => 'required|email',
                'token'    => 'required',
                'password' => 'required',
            ]
        );
        $user = $repository->getByTokenAndEmail($request->get('token', null), $request->get('email', null));
        if (!($user instanceof User)) {
            return response()->json(
                ['success' => false, 'message' => 'User not authorized for password reset'],
                403
            );
        }
        $user->password           = Hash::make($request->get('password'));
        $user->reset_token        = null;
        $user->token_requested_at = null;
        $user->save();
        return response()->json(['success' => true, 'message' => 'password changed']);
    }
}
