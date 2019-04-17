<?php


namespace App\Repositories;


use App\Mail\MailPasswordReset;
use App\Notification;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class UserRepository
{
    /**
     * @param Collection $notifications
     *
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    public function findByNotifications(Collection $notifications)
    {
        $notificationIds = $notifications->map(
            function ($notification) {
                return $notification->id;
            }
        );
        // Get all the users that have a notification with a forecast that matches the params and set notifcations to the ones with a matching forecast
        return User::query()->whereHas(
            'notifications',
            function (Builder $q) use ($notificationIds) {
                $q->whereIn('id', $notificationIds);
            }
        )->get()->map(
            function (User $user) use ($notifications) {
                $user->notifications = $notifications->filter(
                    function (Notification $notification) use ($user) {
                        return $notification->user_id === $user->id;
                    }
                );
                return $user;
            }
        );
    }

    /**
     * @param $length
     *
     * @return string
     * @throws \Exception
     */
    public function getToken($length)
    {
        $token        = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max          = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }


    /**
     * @param $user
     *
     * @return mixed
     * @throws \Exception
     */
    public function createPasswordReset($user)
    {
        $token                    = $this->getToken(32);
        $user->reset_token        = $token;
        $user->token_requested_at = new \DateTime();
        $user->save();
        $url = env('APP_URL') . '/password-reset?email=' . $user->email . '&token=' . $token;
        Mail::to($user->email)->send(
            new MailPasswordReset($user, $url)
        );
        return $user;
    }

    /**
     * @param $token
     * @param $email
     *
     * @return Builder|\Illuminate\Database\Eloquent\Model|mixed
     * @throws \Exception
     */
    public function getByTokenAndEmail($token, $email)
    {
        $compareDate = (new \DateTime())->modify('-1 day');
        return User::query()->where(
            [
                ['email', '=', $email],
                ['reset_token', '=', $token],
                ['token_requested_at', '>', $compareDate]
            ]
        )->first();
    }
}
