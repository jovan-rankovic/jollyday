<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\HolidayDate;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get authenticated user.
     *
     * @param Request $request
     * @return mixed
     */
    private function getCurrentUser(Request $request)
    {
        return $request->user();
    }

    /**
     * Return authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function current(Request $request): JsonResponse
    {
        return response()->json($this->getCurrentUser($request));
    }

    /**
     * Return remaining holiday days.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function remainingDays(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser($request);
        $employmentDate = $user->employment_date;
        $interval = date_diff(new DateTime($employmentDate), now());
        $monthsSinceEmployment = $interval->format('%m');
        $daysEarned = $monthsSinceEmployment * 2;
        $usedDays = count($user->holidayDates);

        while ($monthsSinceEmployment >= 12) {
            $daysEarned -= 24;
            $usedDays -= 24;
        }

        $usedDays = count($user->holidayDates);

        return response()->json($daysEarned - $usedDays);
    }

    /**
     * Return holiday dates.
     *
     * @param Request $request
     * @return array
     */
    public function holidayDates(Request $request): array
    {
        $userDates = $this->getCurrentUser($request)->holidayDates;
        $datesArray = [];

        foreach ($userDates as $userDate) {
            $datesArray[] = $userDate->date;
        }

        return $datesArray;
    }

    /**
     * Submit selected holiday days.
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function submitDays(Request $request): void
    {
        $userId = $this->getCurrentUser($request)->id;
        $start = new DateTime($request->start_date);
        $end = new DateTime($request->end_date);
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end);

        foreach ($period as $p) {

            HolidayDate::create([
                'date' => $p->format('Y-m-d'),
                'user_id' => $userId
            ]);
        }
    }
}
