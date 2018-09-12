<?php

namespace App\Http\Controllers\Api\V1;

use App\Activity;
use App\GroupType;
use App\Http\Controllers\Controller;
use App\Interest;
use App\Job;
use App\Organization;

class ConstantController extends Controller
{
    //
    public function getActivities()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $activities_count = Activity::count();
        $activities = Activity::orderBy('created_at', 'desc')->get();

        return response()->json([
            'activities' => $activities,
            'status' => true
        ]);
    }

    public function getOrganizations()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $organizations_count = Organization::count();
        $organizations = Organization::orderBy('created_at', 'desc')->get();
        return response()->json([
            'organizations' => $organizations,
            'status' => true
        ]);

    }

    public function getInterests()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $interests_count = Interest::count();
        $interests = Interest::orderBy('created_at', 'desc')->get();

        return response()->json([
            'interests' => $interests,
            'status' => true
        ]);

    }

    public function getJobs()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $jobs_count = Job::count();
        $jobs = Job::orderBy('created_at', 'desc')->get();

        return response()->json([
            'jobs' => $jobs,
            'status' => true
        ]);
    }

    public function getGroupTypes()
    {
        $paginate_num = 0;
        if (isset($_GET['page']) && intval($_GET['page']) > 0) {
            $paginate_num = intval($_GET['page']);
        }

        $group_types_count = GroupType::count();
        $group_types = GroupType::orderBy('created_at', 'desc')->get();

        return response()->json([
            'group_types' => $group_types,
            'status' => true
        ]);

    }
}
