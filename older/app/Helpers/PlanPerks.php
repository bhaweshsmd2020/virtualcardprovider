<?php

namespace App\Helpers;

use Carbon\Carbon;

class PlanPerks
{
    public static function checkPlan(string $planKey, bool $toArray = false)
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        $keys = collect([
            // numeric
            'cards' => $user->credit_cards()->count(),
        ]);

        $resourceCount = $keys->toArray()[$planKey] ?? null;

        if ($resourceCount === null || $toArray) {
            $errorMsg = ['status' => 'danger', 'message' => __('Plan key not exist or user data mismatch!')];
            return $toArray ? $errorMsg : throw new \App\Exceptions\SessionException($errorMsg['message']);
        }

        $expirationDate = Carbon::parse($user->will_expire);

        $error = ['status' => null, 'message' => ''];

        if (!$user->plan_data) {
            $error = ['status' => 'danger', 'message' => 'You have not purchased a plan.'];
        }

        if ($expirationDate->isPast()) {
            $error = ['status' => 'danger', 'message' => 'Your plan has expired. Please renew your plan!'];
        }

        if (is_array($user->plan_data) && !empty($user->plan_data)) {
            $planValue = $user->plan_data[$planKey]['value'];

            if ($planValue === -1) {
                $error = ['status' => 'success'];
            }
            if (is_bool($planValue)) {
                $error = $planValue ? ['status' => 'success'] : ['status' => 'danger', 'message' => 'The feature is not available in your plan. Please upgrade your plan.'];
            }
            if ($resourceCount && $resourceCount >= $planValue) {
                $error = ['status' => 'danger', 'message' => 'You have reached your ' . $planKey . ' limit. Please upgrade your plan.'];
            }
        }

        if ($error['status'] == 'danger' || $toArray) {
            return $toArray ? $error : throw new \App\Exceptions\SessionException(str($error['message'] ?? 'Something went wrong')->headline());
        }

        return $error;
    }
}
