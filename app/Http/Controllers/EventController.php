<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Validator;

use App\Event;
use App\EventZone;
use App\CustomEventTag;
use App\VictoryPoinMultiplier;
use App\EventMember;
use App\EventJudge;
use App\EventSponsor;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use App\EventJudgeMemberAssignment;
use App\EventMemberClass;
use App\EventJudgeActivity;
use App\CanTuneConsumerPyramid;
use App\CanTuneProsumerPyramid;
use App\CanTuneProfessionalPyramid;
use App\ClassCategory;
use App\ClassGrade;
use App\ClassGroup;
use App\CompetitionActivityClassGrade;
use App\EventActivityClassForm;
use App\EventTagGroup;
use App\EventType;
use App\FormGenerator;
use App\VictoryPointMultiplier;
use App\User;
use DateTime;
use Carbon\Carbon;

class EventController extends Controller
{

    public $successStatus = 200;

    public function listWinnerShowcase(Request $request)
    {
        $event_id = $request->event_id;
        $class_group_id = $request->class_group_id;
        $competition_activity_id = $request->competition_activity_id;
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;
        $search = $request->search;

        $date = date('Y-m-d');

        if ($event_id === null && $class_group_id === null && $competition_activity_id === null) { // KOSONG SEMUA
            $event = Event::where('date_start', '<=', $date)->where('status_score_final', 1)->orderBy('date_time_start', 'desc')->orderBy('created_at', 'desc')->first();
            $search = '';

            if ($event) {
                $arr_competition_activity = json_decode($event->competition_activity, true);

                $firstTrue = 0;
                for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                    $eventMemberClass = new EventMemberClass();
                    $status = $eventMemberClass->getActivityStatusOfAssessment($event->id, $arr_competition_activity[$i]['id']);
                    $arr_competition_activity[$i]['status'] = $status;

                    if ($firstTrue === 0 && $status === true) {
                        $firstTrue = 1;

                        $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    } else {
                        $class_group = $eventMemberClass->getClassGroupAssessedOfEvent($event_id, $arr_competition_activity[$i]['id']);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    }
                }

                $event->competition_activity = $arr_competition_activity;

                return response()->json(['status' => 'success', 'data' => $event], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event], 200);
            }
        } else if ($event_id !== null && $class_group_id === null && $competition_activity_id === null) { // ADA EVENT ID
            $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->where('status_score_final', 1)->first();
            $search = '';

            if ($event) {
                $arr_competition_activity = json_decode($event->competition_activity, true);

                $firstTrue = 0;
                for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                    $eventMemberClass = new EventMemberClass();
                    $status = $eventMemberClass->getActivityStatusOfAssessment($event->id, $arr_competition_activity[$i]['id']);
                    $arr_competition_activity[$i]['status'] = $status;

                    if ($firstTrue === 0 && $status === true) {
                        $firstTrue = 1;

                        $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    } else {
                        $class_group = $eventMemberClass->getClassGroupAssessedOfEvent($event_id, $arr_competition_activity[$i]['id']);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    }
                }

                $event->competition_activity = $arr_competition_activity;

                return response()->json(['status' => 'success', 'data' => $event], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event], 200);
            }
        } else if ($event_id !== null && $class_group_id !== null && $competition_activity_id !== null) { // SEMUA TERISI
            $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->where('status_score_final', 1)->first();

            $competition_activity_id = (int) $competition_activity_id;
            $class_group_id = (int) $class_group_id;

            if ($event) {
                $arr_competition_activity = json_decode($event->competition_activity, true);

                for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                    $arr_competition_activity[$i]['id'] = (int) $arr_competition_activity[$i]['id'];

                    $eventMemberClass = new EventMemberClass();
                    $status = $eventMemberClass->getActivityStatusOfAssessment($event->id, $arr_competition_activity[$i]['id']);
                    $arr_competition_activity[$i]['status'] = $status;

                    if ($status === true && $competition_activity_id === $arr_competition_activity[$i]['id']) {
                        $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    } else {
                        $class_group = $eventMemberClass->getClassGroupAssessedOfEvent($event_id, $arr_competition_activity[$i]['id']);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    }


                    // if ($firstTrue === 0 && $status === true) {
                    //     $firstTrue = 1;

                    //     $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                    //     $arr_competition_activity[$i]['class_group'] = $class_group;
                    // }
                }

                $event->competition_activity = $arr_competition_activity;

                return response()->json(['status' => 'success', 'data' => $event], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event], 200);
            }
        }

        return response()->json(['status' => 'failed', 'event_id' => $event_id, 'class_group_id' => $class_group_id, 'competition_activity_id' => $competition_activity_id], 200);
    }

    public function listWinnerShowcase2(Request $request)
    {
        $event_id = $request->event_id;
        $role_id = $request->role_id;
        $country_id = $request->country_id;
        $class_group_id = $request->class_group_id;
        $competition_activity_id = $request->competition_activity_id;
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;
        $search = $request->search;

        $date = date('Y-m-d');

        if ($event_id === null && $class_group_id === null && $competition_activity_id === null) { // KOSONG SEMUA, Take latest 5 untuk homepage
            // $event = Event::where('date_start', '<=', $date)->where('status_score_final', 1)->orderBy('date_time_start', 'desc')->orderBy('created_at', 'desc')->first();
            if ($country_id === null) {
                //     $event = Event::where('date_start', '<=', $date)->where('status_score_final', 1)->orderBy('date_time_start', 'desc')->orderBy('created_at', 'desc')->take(5)->get();
                $event = Event::where('date_start', '<=', $date)->orderBy('date_time_start', 'desc')->orderBy('created_at', 'desc')->take(5)->get();
            } else {
                // $event = Event::where('event_countries_id', '=', $country_id)->where('date_start', '<=', $date)->where('status_score_final', 1)->orderBy('date_time_start', 'desc')->orderBy('created_at', 'desc')->take(5)->get();
                $event = Event::where(function ($query) use ($country_id) {
                    $query->where('event_countries_id', 'like', $country_id)
                        ->orWhere('event_countries_id', 10); // GLOBAL ID
                })
                    ->where('date_start', '<=', $date)->orderBy('date_time_start', 'desc')->orderBy('created_at', 'desc')->take(5)->get();
            }

            $search = '';

            $event_arr = [];

            if ($event) {

                for ($j = 0; $j < sizeof($event); $j++) {

                    $arr_competition_activity = json_decode($event[$j]['competition_activity'], true);

                    $firstTrue = 0;
                    for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                        $eventMemberClass = new EventMemberClass();
                        $status = $eventMemberClass->getActivityStatusOfAssessment($event[$j]['id'], $arr_competition_activity[$i]['id']);
                        $arr_competition_activity[$i]['status'] = $status;

                        // if ($firstTrue === 0 && $status === true) {
                        //     $firstTrue = 1;

                        $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event[$j]['id'], $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                        // } else {
                        //     $class_group = $eventMemberClass->getClassGroupAssessedOfEvent($event_id, $arr_competition_activity[$i]['id']);
                        //     $arr_competition_activity[$i]['class_group'] = $class_group;
                        // }
                    }

                    $event[$j]['competition_activity'] = $arr_competition_activity;

                    $event_arr[] = $event[$j];
                }

                return response()->json(['status' => 'success', 'data' => $event_arr, 'countryid' => $country_id], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event], 200);
            }
        } else if ($event_id !== null && $class_group_id === null && $competition_activity_id === null) { // ADA EVENT ID untuk event past
            // If admin wants to see the scoring before finalize assessment
            $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->first();
            // if ($role_id == 1 || $role_id == 7) {
            //     $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->first();
            // } else {
            //     $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->where('status_score_final', 1)->first();
            // }
            $search = '';

            if ($event) {
                $arr_competition_activity = json_decode($event->competition_activity, true);

                $firstTrue = 0;
                for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                    $eventMemberClass = new EventMemberClass();
                    $status = $eventMemberClass->getActivityStatusOfAssessment($event->id, $arr_competition_activity[$i]['id']);
                    $arr_competition_activity[$i]['status'] = $status;

                    // if ($firstTrue === 0 && $status === true) {
                    //     $firstTrue = 1;

                    $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                    $arr_competition_activity[$i]['class_group'] = $class_group;
                    // } else {
                    //     $class_group = $eventMemberClass->getClassGroupAssessedOfEvent($event_id, $arr_competition_activity[$i]['id']);
                    //     $arr_competition_activity[$i]['class_group'] = $class_group;
                    // }
                }

                $event->competition_activity = $arr_competition_activity;

                return response()->json(['status' => 'success', 'data' => $event], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event, 'role_id' => $role_id], 200);
            }
        } else if ($event_id !== null && $class_group_id !== null && $competition_activity_id !== null) { // SEMUA TERISI, ini blom terpakai, ini jika mau refresh per class group dan competition activity
            $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->where('status_score_final', 1)->first();

            $competition_activity_id = (int) $competition_activity_id;
            $class_group_id = (int) $class_group_id;

            if ($event) {
                $arr_competition_activity = json_decode($event->competition_activity, true);

                for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                    $arr_competition_activity[$i]['id'] = (int) $arr_competition_activity[$i]['id'];

                    $eventMemberClass = new EventMemberClass();
                    $status = $eventMemberClass->getActivityStatusOfAssessment($event->id, $arr_competition_activity[$i]['id']);
                    $arr_competition_activity[$i]['status'] = $status;

                    if ($status === true && $competition_activity_id === $arr_competition_activity[$i]['id']) {
                        $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    } else {
                        $class_group = $eventMemberClass->getClassGroupAssessedOfEvent($event_id, $arr_competition_activity[$i]['id']);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    }


                    // if ($firstTrue === 0 && $status === true) {
                    //     $firstTrue = 1;

                    //     $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                    //     $arr_competition_activity[$i]['class_group'] = $class_group;
                    // }
                }

                $event->competition_activity = $arr_competition_activity;

                return response()->json(['status' => 'success', 'data' => $event], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event], 200);
            }
        }

        return response()->json(['status' => 'failed', 'event_id' => $event_id, 'class_group_id' => $class_group_id, 'competition_activity_id' => $competition_activity_id], 200);
    }

    public function listWinnerAssociationShowcase2(Request $request)
    {
        $event_id = $request->event_id;
        $role_id = $request->role_id;
        $association_id = $request->association_id;
        $class_group_id = $request->class_group_id;
        $competition_activity_id = $request->competition_activity_id;
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;
        $search = $request->search;

        $date = date('Y-m-d');

        if ($event_id === null && $class_group_id === null && $competition_activity_id === null) {
            if ($association_id === null) {
                $event = Event::where('date_start', '<=', $date)->orderBy('date_time_start', 'desc')->orderBy('created_at', 'desc')->take(5)->get();
            } else {
                $event = Event::where(function ($query) use ($association_id) {
                    $query->where('association_id', 'like', $association_id);
                })
                    ->where('date_start', '<=', $date)->orderBy('date_time_start', 'desc')->orderBy('created_at', 'desc')->take(5)->get();
            }

            $search = '';

            $event_arr = [];

            if ($event) {

                for ($j = 0; $j < sizeof($event); $j++) {

                    $arr_competition_activity = json_decode($event[$j]['competition_activity'], true);

                    $firstTrue = 0;
                    for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                        $eventMemberClass = new EventMemberClass();
                        $status = $eventMemberClass->getActivityStatusOfAssessment($event[$j]['id'], $arr_competition_activity[$i]['id']);
                        $arr_competition_activity[$i]['status'] = $status;

                        $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event[$j]['id'], $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    }

                    $event[$j]['competition_activity'] = $arr_competition_activity;

                    $event_arr[] = $event[$j];
                }

                return response()->json(['status' => 'success', 'data' => $event_arr, 'associationid' => $association_id], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event], 200);
            }
        } else if ($event_id !== null && $class_group_id === null && $competition_activity_id === null) { // ADA EVENT ID untuk event past
            // If admin wants to see the scoring before finalize assessment
            $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->first();
            // if ($role_id == 1 || $role_id == 7) {
            //     $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->first();
            // } else {
            //     $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->where('status_score_final', 1)->first();
            // }
            $search = '';

            if ($event) {
                $arr_competition_activity = json_decode($event->competition_activity, true);

                $firstTrue = 0;
                for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                    $eventMemberClass = new EventMemberClass();
                    $status = $eventMemberClass->getActivityStatusOfAssessment($event->id, $arr_competition_activity[$i]['id']);
                    $arr_competition_activity[$i]['status'] = $status;

                    // if ($firstTrue === 0 && $status === true) {
                    //     $firstTrue = 1;

                    $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                    $arr_competition_activity[$i]['class_group'] = $class_group;
                    // } else {
                    //     $class_group = $eventMemberClass->getClassGroupAssessedOfEvent($event_id, $arr_competition_activity[$i]['id']);
                    //     $arr_competition_activity[$i]['class_group'] = $class_group;
                    // }
                }

                $event->competition_activity = $arr_competition_activity;

                return response()->json(['status' => 'success', 'data' => $event], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event, 'role_id' => $role_id], 200);
            }
        } else if ($event_id !== null && $class_group_id !== null && $competition_activity_id !== null) { // SEMUA TERISI, ini blom terpakai, ini jika mau refresh per class group dan competition activity
            $event = Event::where('id', $event_id)->where('date_start', '<=', $date)->where('status_score_final', 1)->first();

            $competition_activity_id = (int) $competition_activity_id;
            $class_group_id = (int) $class_group_id;

            if ($event) {
                $arr_competition_activity = json_decode($event->competition_activity, true);

                for ($i = 0; $i < sizeof($arr_competition_activity); $i++) {
                    $arr_competition_activity[$i]['id'] = (int) $arr_competition_activity[$i]['id'];

                    $eventMemberClass = new EventMemberClass();
                    $status = $eventMemberClass->getActivityStatusOfAssessment($event->id, $arr_competition_activity[$i]['id']);
                    $arr_competition_activity[$i]['status'] = $status;

                    if ($status === true && $competition_activity_id === $arr_competition_activity[$i]['id']) {
                        $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    } else {
                        $class_group = $eventMemberClass->getClassGroupAssessedOfEvent($event_id, $arr_competition_activity[$i]['id']);
                        $arr_competition_activity[$i]['class_group'] = $class_group;
                    }


                    // if ($firstTrue === 0 && $status === true) {
                    //     $firstTrue = 1;

                    //     $class_group = $eventMemberClass->getClassGroupAndParticipantAssessedOfEvent($event->id, $arr_competition_activity[$i]['id'], $offset, $limit, $search);
                    //     $arr_competition_activity[$i]['class_group'] = $class_group;
                    // }
                }

                $event->competition_activity = $arr_competition_activity;

                return response()->json(['status' => 'success', 'data' => $event], 200);
            } else {
                return response()->json(['status' => 'failed', 'data' => $event], 200);
            }
        }

        return response()->json(['status' => 'failed', 'event_id' => $event_id, 'class_group_id' => $class_group_id, 'competition_activity_id' => $competition_activity_id], 200);
    }

    public function finalizeAssessmentOfEvent(Request $request)
    {
        $event = Event::where('id', $request->id)->first();

        if ($event) {

            if ($event->status_score_final == 1) {
                return response()->json(['status' => 'failed', 'message' => 'Event has already been finalized.'], 200);
            }

            $competition_activity = $event->competition_activity;

            $arr = json_decode($competition_activity, true);

            // return response()->json(['status' => 'success', 'competition_activity_id' => $arr], 200);

            // CHECKS ALL ACTIVITY IF HAS BEEN ASSESSED
            // for ($i = 0; $i < sizeof($arr); $i++) {
            //     $arr[$i]['id'] = (int) $arr[$i]['id'];

            //     if ($arr[$i]['id'] === 1) {
            //         $status = $this->isAllCanQHaveBeenAssessed($request->id);
            //         if ($status === false) {
            //             return response()->json(['status' => 'failed', 'message' => 'finalization declined because all assessment processes have not been completed'], 200);
            //         }
            //     } else if ($arr[$i]['id'] === 2) {
            //         $status = $this->isAllCanLoudHaveBeenAssessed($request->id);
            //         if ($status === false) {
            //             return response()->json(['status' => 'failed', 'message' => 'finalization declined because all assessment processes have not been completed'], 200);
            //         }
            //     } else if ($arr[$i]['id'] === 3) {
            //         $status = $this->isAllCanJamHaveBeenAssessed($request->id);
            //         if ($status === false) {
            //             return response()->json(['status' => 'failed', 'message' => 'finalization declined because all assessment processes have not been completed'], 200);
            //         }
            //     } else if ($arr[$i]['id'] === 4) {
            //         $status = $this->isAllCanCraftHaveBeenAssessed($request->id);
            //         if ($status === false) {
            //             return response()->json(['status' => 'failed', 'message' => 'finalization declined because all assessment processes have not been completed'], 200);
            //         }
            //     } else if ($arr[$i]['id'] === 5) {
            //         $status = $this->isAllCanTuneHaveBeenAssessed($request->id);
            //         if ($status === false) {
            //             return response()->json(['status' => 'failed', 'message' => 'finalization declined because all assessment processes have not been completed'], 200);
            //         }
            //     } else if ($arr[$i]['id'] === 6) {
            //         $status = $this->isAllCanPerformHaveBeenAssessed($request->id);
            //         if ($status === false) {
            //             return response()->json(['status' => 'failed', 'message' => 'finalization declined because all assessment processes have not been completed'], 200);
            //         }
            //     } else if ($arr[$i]['id'] === 7) {
            //         $status = $this->isAllCanDanceHaveBeenAssessed($request->id);
            //         if ($status === false) {
            //             return response()->json(['status' => 'failed', 'message' => 'finalization declined because all assessment processes have not been completed'], 200);
            //         }
            //     }
            // }

            $event_type_id = $event->event_type_id;

            $status_can_final = (int) $event->status_can_final;

            $eventType = EventType::where('id', $event_type_id)->first();

            $factor = (int) $eventType->factor;

            $class_groups = [];
            for ($i = 0; $i < sizeof($arr); $i++) {
                if ($arr[$i]['id'] === 1) {
                    $class_groups = $this->countVictoryPointOfCanQ($request->id, $factor);
                }
            }

            $update = Event::where('id', $request->id)->update(
                [
                    'status_score_final' => 1
                ]
            );

            // if ($update) {
            //     if ($status_can_final === 1) {
            //         $updateUser = User::update(
            //             [
            //                 'can_q_consumer_point' => 0,
            //                 'can_q_prosumer_point' => 0,
            //                 'can_q_professional_point' => 0
            //             ]
            //         );
            //     }
            // }


            return response()->json(['status' => 'success', 'message' => 'Event and assessment has been finalized', 'class_group' => $class_groups], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Event not found.'], 200);
        }
    }

    public function unfinalizeAssessmentOfEvent(Event $event)
    {
        if ($event->status_score_final == 0) {
            return response()->json(
                [
                    "status" => "failed",
                    "message" => "Event hasn't been finalized."
                ],
                200
            );
        }

        $competition_activity = $event->competition_activity;

        $arr = json_decode(
            $competition_activity,
            true
        );

        $event_type_id = $event->event_type_id;

        $eventType = EventType::where('id', $event_type_id)->first();

        $factor = (int) $eventType->factor;

        $class_groups = [];
        for ($i = 0; $i < sizeof($arr); $i++) {
            if ($arr[$i]['id'] === 1) {
                $class_groups = $this->resetVictoryPointOfCanQ(
                    $event->id,
                    $factor
                );
            }
        }

        Event::where('id', $event->id)->update(
            [
                'status_score_final' => 0
            ]
        );

        return response()->json(
            [
                "status" => "success",
                "message" => "Event and assessment has been finalized",
                "class_group" => $class_groups
            ],
            200
        );
    }

    public function countVictoryPointOfCanQ($event_id, $factor)
    {
        // Only top 6 gets points
        // $championCount = 6;

        $event_vp_multiplier = Event::where('id', $event_id)->pluck('vp_multiplier')->first();
        $vp_multiplier = VictoryPointMultiplier::where('id', $event_vp_multiplier)->pluck('vp_multiplier')->first();

        $eventMemberClassGroups = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'event_member_classes.class_group_id AS class_group_id',
            'event_member_classes.class_grade_id AS class_grade_id',
            'event_member_classes.grand_total AS grand_total',
            'users.id AS member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            // 'class_groups.id AS class_group_id',
            'class_groups.name AS class_group_name'
            // 'can_q_scores.grand_total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            // ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('event_member_classes.grand_total')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $event_id)
            ->where('event_members.event_id', '=', $event_id)
            // ->orderBy('can_q_scores.grand_total', 'desc')
            // ->orderBy('event_member_classes.grand_total', 'desc')
            // ->orderBy('users.name', 'asc')
            // ->orderBy('class_groups.id', 'asc')
            // ->groupBy('event_member_classes.class_group_id')
            // ->groupBy('users.id')
            // ->pluck('class_groups.id');
            // ->having('')
            // ->limit(6)
            // ->distinct()
            // ->get(['event_member_classes.class_group_id']);
            ->get()
            // ->keyBy('event_member_classes.class_group_id');
            ->toArray();
        // ->unique('event_member_classes.class_group_id');

        $collection = collect($eventMemberClassGroups);

        $unique = $collection->sortByDesc('grand_total')->groupBy('class_group_id');

        $unique = $unique->values()->all();

        // $collection = collect([
        //     ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
        //     ['name' => 'iPhone 5', 'brand' => 'Apple', 'type' => 'phone'],
        //     ['name' => 'Apple Watch', 'brand' => 'Apple', 'type' => 'watch'],
        //     ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
        //     ['name' => 'Galaxy Gear', 'brand' => 'Samsung', 'type' => 'watch'],
        // ]);

        // $unique = $collection->unique('brand');

        // $unique->values()->all();


        // $eventMemberClass = EventMemberClass::select(
        //     'event_member_classes.id AS event_member_class_id',
        //     'event_member_classes.event_member_id AS event_member_id',
        //     'event_member_classes.class_grade_id AS class_grade_id',
        //     'event_member_classes.grand_total AS grand_total',
        //     'users.id AS member_id',
        //     'users.name AS member_name',
        //     'user_profiles.avatar AS member_avatar',
        //     'class_groups.name AS class_group_name'
        //     // 'can_q_scores.grand_total AS total'
        // )
        //     ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
        //     ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
        //     ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
        //     ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
        //     // ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
        //     ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
        //     ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
        //     ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
        //     ->whereNotNull('event_member_classes.grand_total')
        //     ->where('event_judge_activities.competition_activity_id', '=', 1)
        //     ->where('event_judges.event_id', '=', $event_id)
        //     ->where('event_members.event_id', '=', $event_id)
        //     // ->orderBy('can_q_scores.grand_total', 'desc')
        //     ->orderBy('event_member_classes.grand_total', 'desc')
        //     ->orderBy('users.name', 'asc')
        //     // ->limit(6)
        //     ->get();

        $arr['eventMemberClassGroups'] = $eventMemberClassGroups;
        // $arr['eventMemberClass'] = $eventMemberClass;

        for ($i = 0; $i < sizeof($unique); $i++) {
            $topSixCollection = collect($unique[$i]);

            $topSix = $topSixCollection->take(6)->all();

            // $uniqueSix[$i] = $topSix;

            for ($j = 0; $j < sizeof($topSix); $j++) {

                // Loop through top 1-6
                if ($j === 0) {
                    $victoryPoint = ($factor * 10) * $vp_multiplier;
                } else if ($j === 1) {
                    $victoryPoint = ($factor * 8) * $vp_multiplier;
                } else if ($j === 2) {
                    $victoryPoint = ($factor * 6) * $vp_multiplier;
                } else if ($j === 3) {
                    $victoryPoint = ($factor * 4) * $vp_multiplier;
                } else if ($j === 4) {
                    $victoryPoint = ($factor * 2) * $vp_multiplier;
                } else {
                    $victoryPoint = ($factor * 1) * $vp_multiplier;
                }

                $topSix[$j]['victory_point'] = $victoryPoint;

                $update = EventMemberClass::where('id', $topSix[$j]['event_member_class_id'])->update(
                    [
                        'victory_point' => $victoryPoint
                    ]
                );

                $memberPoint = User::where('id', $topSix[$j]['member_id'])->first();

                $consumerPoint = (int) $memberPoint->can_q_consumer_point;
                $prosumerPoint = (int) $memberPoint->can_q_prosumer_point;
                $professionalPoint = (int) $memberPoint->can_q_professional_point;

                $member = User::where('id', $topSix[$j]['member_id'])->first();

                if ($topSix[$j]['class_grade_id'] == 1) {
                    $member->update(
                        [
                            'can_q_consumer_point' => $consumerPoint + $victoryPoint
                        ]
                    );
                } else if ($topSix[$j]['class_grade_id'] == 2) {
                    $member->update(
                        [
                            'can_q_prosumer_point' => $prosumerPoint + $victoryPoint
                        ]
                    );
                } else if ($topSix[$j]['class_grade_id'] == 3) {
                    $member->update(
                        [
                            'can_q_professional_point' => $professionalPoint + $victoryPoint
                        ]
                    );
                }
            }

            $arr['topSix'][] = $topSix;
            $arr['memberPoint'][] = $memberPoint;
            $arr['member'][] = $member;
        }

        $arr['unique'] = $unique;

        // $arr['topSix'] = $topSix;

        return $arr;

        // $arr = [];

        // foreach ($eventMemberClass as $object) {
        //     $arr[] = $object->toArray();
        // }

        // for ($i = 0; $i < sizeof($arr); $i++) {
        //     if ($i === 0) {
        //         $victoryPoint = $factor * 10;
        //     } else if ($i === 1) {
        //         $victoryPoint = $factor * 8;
        //     } else if ($i === 2) {
        //         $victoryPoint = $factor * 6;
        //     } else if ($i === 3) {
        //         $victoryPoint = $factor * 4;
        //     } else if ($i === 4) {
        //         $victoryPoint = $factor * 2;
        //     } else {
        //         $victoryPoint = $factor * 1;
        //     }

        //     $update = EventMemberClass::where('id', $arr[$i]['event_member_class_id'])->update(
        //         [
        //             'victory_point' => $victoryPoint
        //         ]
        //     );

        //     $member = User::where('id', $arr[$i]['member_id'])->first();

        //     $consumerPoint = (int) $member->can_q_consumer_point;
        //     $prosumerPoint = (int) $member->can_q_prosumer_point;
        //     $professionalPoint = (int) $member->can_q_professional_point;


        //     if ($arr[$i]['class_grade_id'] === 1) {
        //         $updateMemberPoint = User::where('id', $arr[$i]['member_id'])->update(
        //             [
        //                 'can_q_consumer_point' => $consumerPoint + $victoryPoint
        //             ]
        //         );
        //     } else if ($arr[$i]['class_grade_id'] === 2) {
        //         $updateMemberPoint = User::where('id', $arr[$i]['member_id'])->update(
        //             [
        //                 'can_q_prosumer_point' => $prosumerPoint + $victoryPoint
        //             ]
        //         );
        //     } else if ($arr[$i]['class_grade_id'] === 3) {
        //         $updateMemberPoint = User::where('id', $arr[$i]['member_id'])->update(
        //             [
        //                 'can_q_professional_point' => $professionalPoint + $victoryPoint
        //             ]
        //         );
        //     }
        // }
    }

    public function resetVictoryPointOfCanQ($event_id, $factor)
    {

        $event_vp_multiplier = Event::where('id', $event_id)->pluck('vp_multiplier')->first();
        $vp_multiplier = VictoryPointMultiplier::where('id', $event_vp_multiplier)->pluck('vp_multiplier')->first();

        $eventMemberClassGroups = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'event_member_classes.class_group_id AS class_group_id',
            'event_member_classes.class_grade_id AS class_grade_id',
            'event_member_classes.grand_total AS grand_total',
            'users.id AS member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('event_member_classes.grand_total')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $event_id)
            ->where('event_members.event_id', '=', $event_id)
            ->get()
            ->toArray();

        $collection = collect($eventMemberClassGroups);

        $unique = $collection->sortByDesc('grand_total')->groupBy('class_group_id');

        $unique = $unique->values()->all();

        $arr['eventMemberClassGroups'] = $eventMemberClassGroups;

        for ($i = 0; $i < sizeof($unique); $i++) {
            $topSixCollection = collect($unique[$i]);

            $topSix = $topSixCollection->take(6)->all();

            for ($j = 0; $j < sizeof($topSix); $j++) {

                if ($j === 0) {
                    $victoryPoint = ($factor * 10) * $vp_multiplier;
                } else if ($j === 1) {
                    $victoryPoint = ($factor * 8) * $vp_multiplier;
                } else if ($j === 2) {
                    $victoryPoint = ($factor * 6) * $vp_multiplier;
                } else if ($j === 3) {
                    $victoryPoint = ($factor * 4) * $vp_multiplier;
                } else if ($j === 4) {
                    $victoryPoint = ($factor * 2) * $vp_multiplier;
                } else {
                    $victoryPoint = ($factor * 1) * $vp_multiplier;
                }

                $topSix[$j]['victory_point'] = $victoryPoint;

                EventMemberClass::where('id', $topSix[$j]['event_member_class_id'])->update(
                    [
                        'victory_point' => 0
                    ]
                );

                $memberPoint = User::where('id', $topSix[$j]['member_id'])->first();

                $consumerPoint = (int) $memberPoint->can_q_consumer_point;
                $prosumerPoint = (int) $memberPoint->can_q_prosumer_point;
                $professionalPoint = (int) $memberPoint->can_q_professional_point;

                $member = User::where('id', $topSix[$j]['member_id'])->first();

                if ($topSix[$j]['class_grade_id'] == 1) {
                    $member->update(
                        [
                            'can_q_consumer_point' => $consumerPoint - $victoryPoint
                        ]
                    );
                } else if ($topSix[$j]['class_grade_id'] == 2) {
                    $member->update(
                        [
                            'can_q_prosumer_point' => $prosumerPoint - $victoryPoint
                        ]
                    );
                } else if ($topSix[$j]['class_grade_id'] == 3) {
                    $member->update(
                        [
                            'can_q_professional_point' => $professionalPoint - $victoryPoint
                        ]
                    );
                }
            }

            $arr['topSix'][] = $topSix;
            $arr['memberPoint'][] = $memberPoint;
            $arr['member'][] = $member;
        }

        $arr['unique'] = $unique;

        return $arr;
    }

    public function isAllCanQHaveBeenAssessed($event_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNull('can_q_scores.id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', 1)
            ->count();

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function isAllCanLoudHaveBeenAssessed($event_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_loud_scores', 'can_loud_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNull('can_loud_scores.id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', 2)
            ->count();

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function isAllCanJamHaveBeenAssessed($event_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNull('can_jam_score_histories.id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', 3)
            ->count();

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function isAllCanCraftHaveBeenAssessed($event_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNull('can_craft_scores.id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', 4)
            ->count();

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function isAllCanTuneHaveBeenAssessed($event_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->count();

        if ($count > 0) {
            if ($count >= 2 && $count <= 6) {
                $countConsumer = EventMemberClass::select(
                    'event_member_classes.id AS event_member_class_id'
                )
                    ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                    ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                    ->leftJoin('can_tune_consumer_pyramids', 'can_tune_consumer_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                    ->whereNotNull('event_judge_member_assignments.id')
                    ->where('event_members.event_id', '=', $event_id)
                    ->where('event_member_classes.competition_activity_id', '=', 5)
                    ->where('event_member_classes.class_grade_id', '=', 1)
                    ->where(function ($query) {
                        $query->where('can_tune_consumer_pyramids.status_assessment', '=', 0)
                            ->OrWhereNull('can_tune_consumer_pyramids.status_assessment');
                    })
                    ->count();

                if ($countConsumer > 0) {
                    return false;
                } else {
                    return true;
                }
            } else if ($count >= 7 && $count <= 18) {
                $countProsumer = EventMemberClass::select(
                    'event_member_classes.id AS event_member_class_id'
                )
                    ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                    ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                    ->leftJoin('can_tune_prosumer_pyramids', 'can_tune_prosumer_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                    ->whereNotNull('event_judge_member_assignments.id')
                    ->where('event_members.event_id', '=', $event_id)
                    ->where('event_member_classes.competition_activity_id', '=', 5)
                    ->where('event_member_classes.class_grade_id', '=', 2)
                    ->where(function ($query) {
                        $query->where('can_tune_prosumer_pyramids.status_assessment', '=', 0)
                            ->OrWhereNull('can_tune_prosumer_pyramids.status_assessment');
                    })
                    ->count();

                if ($countProsumer > 0) {
                    return false;
                } else {
                    return true;
                }
            } else if ($count >= 19 && $count <= 36) {
                $countProfessional = EventMemberClass::select(
                    'event_member_classes.id AS event_member_class_id'
                )
                    ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                    ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                    ->leftJoin('can_tune_professional_pyramids', 'can_tune_professional_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                    ->whereNotNull('event_judge_member_assignments.id')
                    ->where('event_members.event_id', '=', $event_id)
                    ->where('event_member_classes.competition_activity_id', '=', 5)
                    ->where('event_member_classes.class_grade_id', '=', 3)
                    ->where(function ($query) {
                        $query->where('can_tune_professional_pyramids.status_assessment', '=', 0)
                            ->OrWhereNull('can_tune_professional_pyramids.status_assessment');
                    })
                    ->count();

                if ($countProfessional > 0) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function isAllCanPerformHaveBeenAssessed($event_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_perform_scores', 'can_perform_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNull('can_perform_scores.id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', 6)
            ->count();

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function isAllCanDanceHaveBeenAssessed($event_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNull('can_dance_scores.id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', 7)
            ->count();

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }


    // OKE
    public function listAndCountUpcomingLimit(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        if ($request->country_id != null) {
            $country_id = $request->country_id;
        } else {
            $country_id = "%";
        }

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '>', $date)
            ->where(function ($query) use ($country_id) {
                $query->where('event_countries_id', 'like', $country_id)
                    ->orWhere('event_countries_id', null)
                    ->orWhere('event_countries_id', 10); // GLOBAL ID
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->orderBy('date_start', 'ASC')
            ->limit($request->limit)
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'competition_activity'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '>', $date)
            ->where(function ($query) use ($country_id) {
                $query->where('event_countries_id', 'like', $country_id)
                    ->orWhere('event_countries_id', null)
                    ->orWhere('event_countries_id', 10); // GLOBAL ID
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventSponsor = new EventSponsor();
            $eventMember = new Event();
            $status = $eventMember->getStatusEventByEventId($arr[$i]['id']);
            $result = $eventSponsor->getAllSponsorsByEventId($arr[$i]['id']);
            $arr[$i]['sponsor'] = $result;
            $arr[$i]['status_keterangan'] = $status;
        }
        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    public function listAndCountUpcomingLimitAssociation(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        if ($request->association_id != null) {
            $association_id = $request->association_id;
        } else {
            $association_id = "%";
        }

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'associations.name AS event_associations_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->join('associations', 'associations.id', '=', 'events.association_id')
            ->where('date_start', '>', $date)
            ->where(function ($query) use ($association_id) {
                $query->where('association_id', 'like', $association_id);
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'associations.name AS event_associations_name',
            'competition_activity'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->join('associations', 'associations.id', '=', 'events.association_id')
            ->where('date_start', '>', $date)
            ->where(function ($query) use ($association_id) {
                $query->where('association_id', 'like', $association_id);
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventSponsor = new EventSponsor();
            $eventMember = new Event();
            $status = $eventMember->getStatusEventByEventId($arr[$i]['id']);
            $result = $eventSponsor->getAllSponsorsByEventId($arr[$i]['id']);
            $arr[$i]['sponsor'] = $result;
            $arr[$i]['status_keterangan'] = $status;
        }
        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    // OKE
    public function listUpcomingOrder(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        if ($request->country_id != null) {
            $country_id = $request->country_id;
        } else {
            $country_id = "%";
        }

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '>', $date)
            ->where(function ($query) use ($country_id) {
                $query->where('event_countries_id', 'like', $country_id)
                    ->orWhere('event_countries_id', null)
                    ->orWhere('event_countries_id', 10); // GLOBAL ID
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'contact_phone',
            'contact_name'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '>', $date)
            ->where(function ($query) use ($country_id) {
                $query->where('event_countries_id', 'like', $country_id)
                    ->orWhere('event_countries_id', null)
                    ->orWhere('event_countries_id', 10); // GLOBAL ID
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->orderBy($request->column, $request->sort)
            ->get()->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventSponsor = new EventSponsor();
            $result = $eventSponsor->getAllSponsorsByEventId($arr[$i]['id']);
            $arr[$i]['sponsor'] = $result;
        }

        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    public function listUpcomingOrderAssociation(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        if ($request->association_id != null) {
            $association_id = $request->association_id;
        } else {
            $association_id = "%";
        }

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'associations.name AS event_associations_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->join('associations', 'associations.id', '=', 'events.association_id')
            ->where('date_start', '>', $date)
            ->where(function ($query) use ($association_id) {
                $query->where('association_id', 'like', $association_id);
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'associations.name AS event_associations_name',
            'contact_phone',
            'contact_name'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->join('associations', 'associations.id', '=', 'events.association_id')
            ->where('date_start', '>', $date)
            ->where(function ($query) use ($association_id) {
                $query->where('association_id', 'like', $association_id);
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->orderBy($request->column, $request->sort)
            ->get()->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventSponsor = new EventSponsor();
            $result = $eventSponsor->getAllSponsorsByEventId($arr[$i]['id']);
            $arr[$i]['sponsor'] = $result;
        }

        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    // OKE
    public function listAndCountPastLimit(Request $request)
    {
        $datetime = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        $time = date('H:i:s');

        if ($request->country_id != null && $request->country_id != 'undefined') {
            $country_id = $request->country_id;
        } else {
            $country_id = "%";
        }

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '<=', $date)
            ->where(function ($query) use ($country_id) {
                $query->where('event_countries_id', 'like', $country_id)
                    ->orWhere('event_countries_id', null)
                    ->orWhere('event_countries_id', 10); // GLOBAL ID
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'contact_phone',
            'contact_name'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '<=', $date)
            ->where(function ($query) use ($country_id) {
                $query->where('event_countries_id', 'like', $country_id)
                    ->orWhere('event_countries_id', null)
                    ->orWhere('event_countries_id', 10); // GLOBAL ID
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->get()->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    public function listAndCountPastLimitAssociation(Request $request)
    {
        $datetime = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        $time = date('H:i:s');

        if ($request->association_id != null && $request->association_id != 'undefined') {
            $association_id = $request->association_id;
        } else {
            $association_id = "%";
        }

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'associations.name AS event_associations_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->join('associations', 'associations.id', '=', 'events.association_id')
            ->where('date_start', '<=', $date)
            ->where(function ($query) use ($association_id) {
                $query->where('association_id', 'like', $association_id);
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'associations.name AS event_associations_name',
            'contact_phone',
            'contact_name'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->join('associations', 'associations.id', '=', 'events.association_id')
            ->where('date_start', '<=', $date)
            ->where(function ($query) use ($association_id) {
                $query->where('association_id', 'like', $association_id);
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->get()->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    // OKE
    public function listAllBySponsorId(Request $request)
    {
        $event = EventSponsor::select(
            'event_sponsors.id AS id',
            'event_sponsors.event_id AS event_id',
            'event_sponsors.sponsor_id AS sponsor_id',
            'events.banner AS banner',
            'events.title AS title',
            'events.description AS description',
            'events.recap AS recap',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location',
            'events.contact_phone AS contact_phone',
            'events.contact_name AS contact_name',
            'events.status_can_final AS status_can_final',
            'events.status_score_final AS status_score_final',
            'events.event_type_id AS event_type_id',
            'event_types.name AS event_type_name',
            'events.event_country_id AS event_country_id',
            'class_countries.name AS event_country_name',
            'events.association_id AS event_associations_id',
            'associations.name AS associations_name',
            'events.competition_activity AS competition_activity',
            'event_sponsors.updated_at AS updated_at',
            'event_sponsors.created_at AS created_at'
        )
            ->join('events', 'events.id', '=', 'event_sponsors.event_id')
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin("associations", "associations.id", "=", "events.association_id")
            ->where('event_sponsors.sponsor_id', '=', $request->sponsor_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('event_sponsors.created_at', 'desc')
            ->get();

        $eventCount = EventSponsor::select(
            'event_sponsors.id AS id',
            'event_sponsors.event_id AS event_id',
            'event_sponsors.sponsor_id AS sponsor_id',
            'events.banner AS banner',
            'events.title AS title',
            'events.description AS description',
            'events.recap AS recap',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location',
            'events.contact_phone AS contact_phone',
            'events.contact_name AS contact_name',
            'events.status_can_final AS status_can_final',
            'events.status_score_final AS status_score_final',
            'events.event_type_id AS event_type_id',
            'event_types.name AS event_type_name',
            'events.event_country_id AS event_country_id',
            'class_countries.name AS event_country_name',
            'events.association_id AS event_associations_id',
            'associations.name AS associations_name',
            'events.competition_activity AS competition_activity',
            'event_sponsors.updated_at AS updated_at',
            'event_sponsors.created_at AS created_at'
        )
            ->join('events', 'events.id', '=', 'event_sponsors.event_id')
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin("associations", "associations.id", "=", "events.association_id")
            ->where('event_sponsors.sponsor_id', '=', $request->sponsor_id)
            ->orderBy('event_sponsors.created_at', 'desc')
            ->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }


    // OKE
    public function listPastOrder(Request $request)
    {
        $datetime = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        $time = date('H:i:s');

        if ($request->country_id != null && $request->country_id != 'undefined') {
            $country_id = $request->country_id;
        } else {
            $country_id = "%";
        }

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '<=', $date)
            ->where(function ($query) use ($country_id) {
                $query->where('event_countries_id', 'like', $country_id)
                    ->orWhere('event_countries_id', null)
                    ->orWhere('event_countries_id', 10); // GLOBAL ID
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'contact_phone',
            'contact_name'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '<=', $date)
            ->where(function ($query) use ($country_id) {
                $query->where('event_countries_id', 'like', $country_id)
                    ->orWhere('event_countries_id', null)
                    ->orWhere('event_countries_id', 10); // GLOBAL ID
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->orderBy($request->column, $request->sort)
            ->get()->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    public function listPastOrderAssociation(Request $request)
    {
        $datetime = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        $time = date('H:i:s');

        if ($request->association_id != null && $request->association_id != 'undefined') {
            $association_id = $request->association_id;
        } else {
            $association_id = "%";
        }

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'associations.name AS event_associations_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->join('associations', 'associations.id', '=', 'events.association_id')
            ->where('date_start', '<=', $date)
            ->where(function ($query) use ($association_id) {
                $query->where('association_id', 'like', $association_id);
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'associations.name AS event_associations_name',
            'contact_phone',
            'contact_name'
        )
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->join('associations', 'associations.id', '=', 'events.association_id')
            ->where('date_start', '<=', $date)
            ->where(function ($query) use ($association_id) {
                $query->where('association_id', 'like', $association_id);
            })
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->orderBy($request->column, $request->sort)
            ->get()->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    // OK
    public function listDetail(Request $request)
    {
        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'use_custom_class',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'association_id',
            'zone',
            'event_zones.zone_name as zone_name',
            'tag',
            'event_tag_groups.tag_id as tag_groups',
            'custom_event_tags.tag_name AS tag_name',
            'victory_point_multipliers.vp_multiplier_name',
            'events.vp_multiplier',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'associations.name AS associations_name',
            'competition_activity'
        )
            ->leftJoin('associations', 'associations.id', '=', 'events.association_id')
            ->join('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->leftJoin('event_zones', 'event_zones.id', '=', 'events.zone')
            ->leftJoin('event_tag_groups', 'event_tag_groups.event_id', '=', 'events.id')
            ->leftJoin('custom_event_tags', 'custom_event_tags.id', '=', 'event_tag_groups.tag_id') // Add this line
            ->leftJoin('victory_point_multipliers', 'victory_point_multipliers.id', '=', 'events.vp_multiplier')
            ->with('tags')
            ->where('events.id', '=', $request->id)
            ->get();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventSponsor = new EventSponsor();
            $event = new Event();
            $eventActivityClassForm = new EventActivityClassForm();

            //Competition activity class grade
            $competition_activity = $arr[$i]['competition_activity'];

            $arr[$i]['tags'] = $object->tags;

            $arrCompetitionActivity = json_decode($competition_activity, true);
            // $arrCompetitionActivityClass = [];
            for ($j = 0; $j < sizeof($arrCompetitionActivity); $j++) {
                $id = (int) $arrCompetitionActivity[$j]['id'];
                $arrCompetitionActivity[$j]['class_grades'] = $eventActivityClassForm->getCompetitionActivityClassGrade($id);
                $arrCompetitionActivity[$j]['class_grade_names'] = $eventActivityClassForm->getCompetitionActivityClassName($id);
            }

            $competitions = $event->getCompetitionsOfEvent($competition_activity);
            $result = $eventSponsor->getAllSponsorsByEventId($arr[$i]['id']);
            $arr[$i]['competition_activity_class'] = $arrCompetitionActivity;
            $custom_classes = $event->getCustomClassesOfEvent($arr[$i]['id'], $arrCompetitionActivity);
            $arr[$i]['sponsor'] = $result;
            $arr[$i]['competitions'] = $competitions;
            $arr[$i]['custom_classes'] = $custom_classes;
        }


        return response()->json($arr);
    }


    // OKE
    public function listParticipatedCars(Request $request)
    {
        $event = EventMember::select(
            'event_members.event_id AS event_id',
            'event_members.member_id AS user_id',
            // 'event_members.car_id AS car_id',
            'event_member_classes.car_id AS car_id',
            'cars.avatar AS avatar',
            'cars.engine AS engine',
            'cars.seat AS seat',
            'cars.transmission_type AS transmission_type',
            'cars.vehicle AS vehicle',
            'cars.license_plate AS license_plate',
            'cars.color AS color',
            'cars.front_car_image AS front_car_image',
            'cars.headunits AS headunits',
            'cars.processor AS processor',
            'cars.power_amplifier AS power_amplifier',
            'cars.speakers AS speakers',
            'cars.wires AS wires',
            'cars.other_devices AS other_devices',
            'users.name AS user_name'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->join('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->where('event_members.event_id', '=', $request->id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->distinct()
            ->get();

        $eventCount = EventMember::select(
            'event_members.event_id AS event_id',
            'event_members.member_id AS user_id',
            // 'event_members.car_id AS car_id',
            'event_member_classes.car_id AS car_id',
            'cars.avatar AS avatar',
            'cars.engine AS engine',
            'cars.seat AS seat',
            'cars.transmission_type AS transmission_type',
            'cars.vehicle AS vehicle',
            'cars.license_plate AS license_plate',
            'cars.color AS color',
            'cars.front_car_image AS front_car_image',
            'cars.headunits AS headunits',
            'cars.processor AS processor',
            'cars.power_amplifier AS power_amplifier',
            'cars.speakers AS speakers',
            'cars.wires AS wires',
            'cars.other_devices AS other_devices'
        )
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->join('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->where('event_members.event_id', '=', $request->id)
            ->distinct('car_id')
            ->count('car_id');

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    public function listAssignedEventToJudge(Request $request)
    {
        $date = date('Y-m-d');

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->leftJoin('event_judges', 'event_judges.event_id', '=', 'events.id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
            ->leftJoin('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->join('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '<=', $date)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->whereNotNull('event_judge_member_assignments.id')
            ->offset($request->offset)
            ->limit($request->limit)
            ->distinct()
            ->get();

        $countEvent = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->leftJoin('event_judges', 'event_judges.event_id', '=', 'events.id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
            ->leftJoin('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->join('countries', 'countries.id', '=', 'events.event_countries_id')
            ->where('date_start', '<=', $date)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->whereNotNull('event_judge_member_assignments.id')
            ->distinct()
            ->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $countEvent]);
    }

    public function listAllByJudgeId(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        $event = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_phone',
            'contact_name',
            'status_can_final',
            'status_score_final',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'associations.name AS associations_name',
            'countries.name AS event_countries_name',
            'competition_activity',
            'events.updated_at AS updated_at',
            'events.created_at AS created_at'
        )
            ->leftJoin('event_judges', 'event_judges.event_id', '=', 'events.id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
            ->leftJoin('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->leftJoin('associations', 'associations.id', '=', 'events.association_id')
            ->where(function ($query) use ($request, $date) {
                if ($request->timeline == 'ongoing') {
                    $query->where('date_start', '<=', $date);
                    $query->where('date_end', '>=', $date);
                    $query->orderBy('date_start', 'asc');
                } else if ($request->timeline == 'past') {
                    // $query->where('date_start', '<', $date);
                    $query->where('date_end', '<', $date);
                    $query->orderBy('date_start', 'desc');
                } else {
                    $query->where('date_start', '>', $date);
                    $query->orderBy('date_start', 'desc');
                }
            })
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->whereNotNull('event_judge_member_assignments.id')
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            // ->orderBy($request->column, $request->sort)
            // ->orderBy('date_start', 'asc')
            ->distinct()
            ->get();

        $eventCount = Event::select(
            'events.id AS id',
            'events.user_id AS user_id',
            'banner',
            'title',
            'description',
            'recap',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'event_type_id',
            'event_types.name AS event_type_name',
            'event_country_id',
            'class_countries.name AS event_country_name',
            'event_countries_id',
            'countries.name AS event_countries_name',
            'associations.name AS associations_name',
            'contact_phone',
            'contact_name'
        )
            ->leftJoin('event_judges', 'event_judges.event_id', '=', 'events.id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
            ->leftJoin('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->leftJoin('countries', 'countries.id', '=', 'events.event_countries_id')
            ->leftJoin('associations', 'associations.id', '=', 'events.association_id')
            ->where(function ($query) use ($request, $date) {
                if ($request->timeline == 'ongoing') {
                    $query->where('date_start', '<=', $date);
                    $query->where('date_end', '>=', $date);
                } else if ($request->timeline == 'past') {
                    // $query->where('date_start', '<', $date);
                    $query->where('date_end', '<', $date);
                } else {
                    $query->where('date_start', '>', $date);
                }
            })
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->whereNotNull('event_judge_member_assignments.id')
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('date_start', 'like', '%' . $request->search . '%')
                    ->orWhere('time_start', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->orderBy('date_start', 'asc')
            ->distinct()
            ->get()->count();

        $arr = [];

        foreach ($event as $object) {
            $arr[] = $object->toArray();
        }

        // for ($i = 0; $i < sizeof($arr); $i++) {
        //     $eventSponsor = new EventSponsor();
        //     $result = $eventSponsor->getAllSponsorsByEventId($arr[$i]['id']);
        //     $arr[$i]['sponsor'] = $result;
        // }

        return response()->json(['data' => $arr, 'total' => $eventCount]);
    }

    public function listActivityClassForm(Request $request)
    {
        $eventActivity = EventActivityClassForm::select(
            // 'id AS activity_class_form_id',
            // 'event_activity_class_forms.id AS id',
            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name'
            // 'class_grades.id AS class_grade_id',
            // 'class_grades.name AS class_grade_name',
            // 'form_generators.id AS form_generator_id',
            // 'form_generators.title AS form_generator_title'
        )
            ->join('competition_activities', 'competition_activities.id', '=', 'event_activity_class_forms.competition_activity_id')
            // ->join('class_grades', 'class_grades.id', '=', 'event_activity_class_forms.class_grade_id')
            // ->leftJoin('form_generators', 'form_generators.id', '=', 'event_activity_class_forms.form_generator_id')
            ->where('event_id', '=', $request->id)
            ->orderBy('competition_activities.id', 'asc')
            // ->orderBy('class_grades.id', 'asc')
            ->groupBy('competition_activities.id')
            ->groupBy('competition_activities.name')
            ->get();

        $arrEventActivity = [];

        foreach ($eventActivity as $object) {
            $arrEventActivity[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arrEventActivity); $i++) {
            # code...

            $classGrades = CompetitionActivityClassGrade::select(
                'class_grades.id AS class_grade_id',
                'class_grades.name AS class_grade_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'competition_activity_class_grades.class_grade_id')
                ->where('competition_activity_id', '=', $arrEventActivity[$i]['competition_activity_id'])
                ->orderBy('class_grades.id', 'asc')
                ->get();

            $arrClassGrades = [];

            foreach ($classGrades as $object) {
                $arrClassGrades[] = $object->toArray();
            }

            for ($j = 0; $j < sizeof($arrClassGrades); $j++) {

                $eventClassForm = EventActivityClassForm::select(
                    'form_generators.id AS form_generator_id',
                    'form_generators.title AS form_generator_title'
                )
                    ->leftJoin('form_generators', 'form_generators.id', '=', 'event_activity_class_forms.form_generator_id')
                    ->where('event_id', '=', $request->id)
                    ->where('class_grade_id', '=', $arrClassGrades[$j]['class_grade_id'])
                    ->where('competition_activity_id', '=', $arrEventActivity[$i]['competition_activity_id'])
                    ->get();

                $arrEventClassForm = [];

                foreach ($eventClassForm as $object) {
                    $arrEventClassForm[] = $object->toArray();
                }

                $arrClassGrades[$j]['forms'] = $arrEventClassForm;
            }

            $arrEventActivity[$i]['class_grades'] = $arrClassGrades;
        }


        // $arr = [];

        return response()->json(['data' => $arrEventActivity]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:events',
            'description' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'location' => 'required',
            'status_can_final' => 'required',
            // 'use_custom_class' => 'required',
            'event_type_id' => 'required',
            'competition_activity' => 'required',
            'zone' => 'required',
            'vp_multiplier' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // Jika status_can_final === 1, cek jika dalam rentang januari - desember tahun berlangsung sudah ada event CAN Final
        $status_can_final = (int) $request->status_can_final;

        if ($status_can_final === 1) {
            $start = date_create($request->date_start);
            // $start = new DateTime($request->date_start);
            $year = date_format($start, "Y");

            // $year = '2019';

            $startDate = $year . '-01-01 00:00:00';
            $endDate = $year . '-12-31 23:59:59';

            $test = Event::whereBetween('date_time_start', [$startDate, $endDate])->whereBetween('date_time_end', [$startDate, $endDate])->where('status_can_final', 1)->first();

            if ($test) {
                return response()->json(['status' => 'failed', 'message' => 'CAN Final have been held once in this entire year. Therefore, this event can not be the CAN Final.'], 200);
            }
            // else {
            //     return response()->json(['status' => 'failed', 'message' => 'CAN Final have been held once in this entire year. Therefore, this event can not be the CAN Final.'], 200);

            // }
        }

        // return response()->json(['status' => 'success', 'message' => $request->sponsor_id], $this->successStatus);

        // BEGIN STORE

        $storagePath = public_path('upload/files/');
        $uploadPath = public_path('upload/');

        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');

        $bannerNameArr = explode(' ', $request->title);
        $bannerName = implode('-', $bannerNameArr);

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath);
        }

        if (!file_exists($storagePath)) {
            mkdir($storagePath);
        }

        if (!file_exists($storagePath . 'event-banner')) {
            mkdir($storagePath . 'event-banner');
        }

        if ($request->banner !== '' && $request->banner !== null) {
            $image_parts = explode(";base64,", $request->banner);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $fileBannerLink = $storagePath . 'event-banner/' . $bannerName . $waktu . '.' . $image_type;
            $fileBannerUrl = $protocol . '://' . $domain . '/upload/files/event-banner/' . $bannerName . $waktu . '.' . $image_type;
            file_put_contents($fileBannerLink, $image_base64);
        } else {
            $fileBannerLink = '';
            $fileBannerUrl = '';
        }

        $input = $request->all();

        $dateStart = date_create($input['date_start']);
        $input['date_start'] = date_format($dateStart, "Y-m-d");

        $dateEnd = date_create($input['date_end']);
        $input['date_end'] = date_format($dateEnd, "Y-m-d");

        $input['banner'] = $fileBannerUrl;

        $input['date_time_start'] = $input['date_start'] . ' ' . $input['time_start'] . ':00';
        $input['date_time_end'] = $input['date_end'] . ' ' . $input['time_end'] . ':00';

        $now = time();
        $date_start = strtotime($input['date_start']);

        // if ($date_start < $now) {
        // $input['status_score_final'] = 1;
        // }


        $saveEvent = Event::create($input);

        if ($saveEvent) {
            $eventId = $saveEvent->id;
            $event = Event::where('title', $request->title)->first();

            $arrSponsorId = json_decode($request->sponsor_id, true);

            if ($arrSponsorId !== null) {
                for ($i = 0; $i < sizeof($arrSponsorId); $i++) {
                    $inputSponsor['event_id'] = $event->id;
                    $inputSponsor['sponsor_id'] = $arrSponsorId[$i]['id'];

                    $check = EventSponsor::where('event_id', $inputSponsor['event_id'])->where('sponsor_id', $inputSponsor['sponsor_id'])->count();

                    if ($check === 0) {
                        $saveSponsor = EventSponsor::create($inputSponsor);
                    }
                }
            }

            $result = $this->storeEventActivityClassForm($request, $event);

            if ($request->use_custom_class == 1) {
                $classGroups = $this->storeClassGroups($request, $event);
            } else {
                $classGroups = null;
            }

            $selectedTags = json_decode($request->selected_tags, true);

            if ($selectedTags) {

                foreach ($selectedTags as $tag) {
                    $tagData = [
                        "event_id" => $eventId,
                        "tag_id" => $tag,
                    ];

                    $check = EventTagGroup::where('event_id', $tagData['event_id'])
                        ->where('tag_id', $tagData['tag_id'])
                        ->count();

                    if ($check === 0) {
                        $eventTags = EventTagGroup::create($tagData);

                        if ($eventTags) {
                            $tagId = $eventTags->id;

                            Event::where('id', $eventId)->update([
                                'tag' => $tagId
                            ]);
                        }
                    }
                }
            }
            return response()->json(['status' => 'success', 'message' => 'created successfully', 'res' => $result, 'classGroups' => $classGroups, 'selectedTags' => $selectedTags], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $event = Event::select('id', 'title')
            ->where('title', $request->title)
            ->get();

        $eventOld = Event::select(
            'id',
            'banner',
            'title',
            'description',
            'event_country_id',
            'event_countries_id',
            'association_id',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'location',
            'contact_name',
            'contact_phone',
            'status_can_final',
            'event_type_id',
            'competition_activity',
            'zone',
            'vp_multiplier'
        )
            ->where('id', $request->id)
            ->get();

        $count = $event->count();

        if ($count > 0) {
            if ($eventOld[0]->id !== $event[0]->id) {
                return response()->json(['status' => 'failed', 'message' => 'title have been used'], 200);
            } else {
                return $this->updateEvent($request, $eventOld);
            }
        } else {
            return $this->updateEvent($request, $eventOld);
        }
    }

    protected function updateEvent(Request $request, $eventOld)
    {
        // Jika status_can_final === 1, cek jika dalam rentang januari - desember tahun berlangsung sudah ada event CAN Final
        $status_can_final = (int) $request->status_can_final;

        if ($status_can_final === 1) {
            $start = date_create($request->date_start);
            // $start = new DateTime($request->date_start);
            $year = date_format($start, "Y");

            // $year = '2019';

            $startDate = $year . '-01-01 00:00:00';
            $endDate = $year . '-12-31 23:59:59';

            $test = Event::whereBetween('date_time_start', [$startDate, $endDate])->whereBetween('date_time_end', [$startDate, $endDate])->where('status_can_final', 1)->where('id', '<>', $request->id)->first();

            if ($test) {
                return response()->json(['status' => 'failed', 'message' => 'CAN Final have been held once in this entire year. Therefore, this event can not be the CAN Final.'], 200);
            }
            // else {
            //     return response()->json(['status' => 'failed', 'message' => 'CAN Final have been held once in this entire year. Therefore, this event can not be the CAN Final.'], 200);

            // }
        }

        $storagePath = public_path('upload/files/');
        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');

        $bannerNameArr = explode(' ', $request->title);
        $bannerName = implode('-', $bannerNameArr);

        $cekImage = substr($request['banner'], 0, 10);

        if ($cekImage === 'data:image') {
            if ($eventOld[0]->banner !== '' && $eventOld[0]->banner !== null) {
                $path = parse_url($eventOld[0]->banner, PHP_URL_PATH);
                $file_name = basename($path);
                $fileBannerLink = $storagePath . 'event-banner/' . $file_name;

                if (file_exists($fileBannerLink)) {
                    unlink($fileBannerLink);
                }
            }
            $image_parts = explode(";base64,", $request->banner);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $fileBannerLink = $storagePath . 'event-banner/' . $bannerName . $waktu . '.' . $image_type;
            $fileBannerUrl = $protocol . '://' . $domain . '/upload/files/event-banner/' . $bannerName . $waktu . '.' . $image_type;
            file_put_contents($fileBannerLink, $image_base64);
        } else {
            $fileBannerUrl = $request['banner'];
        }

        $eventUpdate['banner'] = $fileBannerUrl;
        $eventUpdate['description'] = $request->description;
        $eventUpdate['title'] = $request->title;

        $dateStart = date_create($request->date_start);
        $request->date_start = date_format($dateStart, "Y-m-d");
        $eventUpdate['date_start'] = $request->date_start;

        $dateEnd = date_create($request->date_end);
        $request->date_end = date_format($dateEnd, "Y-m-d");
        $eventUpdate['date_end'] = $request->date_end;

        $eventUpdate['time_start'] = $request->time_start;
        $eventUpdate['time_end'] = $request->time_end;
        $eventUpdate['location'] = $request->location;
        $eventUpdate['contact_name'] = $request->contact_name;
        $eventUpdate['contact_phone'] = $request->contact_phone;
        $eventUpdate['status_can_final'] = $request->status_can_final;
        $eventUpdate['event_type_id'] = $request->event_type_id;
        $eventUpdate['event_country_id'] = $request->event_country_id;
        $eventUpdate['association_id'] = $request->association_id;
        $eventUpdate['event_countries_id'] = $request->event_countries_id;
        $eventUpdate['use_custom_class'] = $request->use_custom_class;
        $eventUpdate['competition_activity'] = $request->competition_activity;
        $eventUpdate['zone'] = $request->zone;
        $eventUpdate['vp_multiplier'] = $request->vp_multiplier;

        $eventUpdate['date_time_start'] = $eventUpdate['date_start'] . ' ' . $eventUpdate['time_start'] . ':00';
        $eventUpdate['date_time_end'] = $eventUpdate['date_end'] . ' ' . $eventUpdate['time_end'] . ':00';

        // Update the event_tag_groups based on changes in selectedTags
        $eventId = $request->id;
        $selectedTags = json_decode($request->selected_tags, true);

        // Get existing tags for the event
        $existingTags = EventTagGroup::where('event_id', $eventId)->pluck('tag_id')->toArray();

        // Get soft-deleted tags for the event
        $softDeletedTags = EventTagGroup::withTrashed()
            ->where('event_id', $eventId)
            ->onlyTrashed()
            ->pluck('tag_id')
            ->toArray();

        // Check if there's a difference between existing tags and selectedTags
        $tagsChanged = count($existingTags) !== count($selectedTags) || count(array_diff($existingTags, $selectedTags)) > 0;

        if ($tagsChanged) {
            // Soft delete existing records not present in selectedTags
            $tagsToDelete = array_diff($existingTags, $selectedTags);
            EventTagGroup::where('event_id', $eventId)->whereIn('tag_id', $tagsToDelete)->delete();

            // Restore soft-deleted tags present in selectedTags
            $softDeletedTagsToRestore = array_intersect($softDeletedTags, $selectedTags);
            if (!empty($softDeletedTagsToRestore)) {
                EventTagGroup::withTrashed()
                    ->where('event_id', $eventId)
                    ->whereIn('tag_id', $softDeletedTagsToRestore)
                    ->restore();
            }

            // Insert new records from selectedTags not present in existingTags or softDeletedTags
            $tagsToAdd = array_diff($selectedTags, array_merge($existingTags, $softDeletedTags));
            foreach ($tagsToAdd as $tagId) {
                EventTagGroup::create([
                    'event_id' => $eventId,
                    'tag_id' => $tagId,
                ]);
            }
        }

        $update = Event::where('id', $request->id)->update(
            [
                'banner' => $eventUpdate['banner'],
                'title' => $eventUpdate['title'],
                'description' => $eventUpdate['description'],
                'date_start' => $eventUpdate['date_start'],
                'date_end' => $eventUpdate['date_end'],
                'time_start' => $eventUpdate['time_start'],
                'time_end' => $eventUpdate['time_end'],
                'location' => $eventUpdate['location'],
                'contact_name' => $eventUpdate['contact_name'],
                'contact_phone' => $eventUpdate['contact_phone'],
                'status_can_final' => $eventUpdate['status_can_final'],
                'event_type_id' => $eventUpdate['event_type_id'],
                'event_country_id' => $eventUpdate['event_country_id'],
                'association_id' => $eventUpdate['association_id'],
                'event_countries_id' => $eventUpdate['event_countries_id'],
                'use_custom_class' => $eventUpdate['use_custom_class'],
                'competition_activity' => $eventUpdate['competition_activity'],
                'zone' => $eventUpdate['zone'],
                'vp_multiplier' => $eventUpdate['vp_multiplier'],
            ]
        );

        if ($update) {

            $this->storeClassGroups($request, $eventOld[0]);

            return $this->updateEventSponsor($request, $eventOld);

            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    protected function updateEventSponsor(Request $request, $eventOld)
    {
        $delete = EventSponsor::where('event_id', $request->id)->delete();

        $arrSponsorId = json_decode($request->sponsor_id, true);

        if ($arrSponsorId !== null) {
            for ($i = 0; $i < sizeof($arrSponsorId); $i++) {
                $inputSponsor['event_id'] = $request->id;
                $inputSponsor['sponsor_id'] = $arrSponsorId[$i]['id'];

                $check = EventSponsor::where('event_id', $inputSponsor['event_id'])->where('sponsor_id', $inputSponsor['sponsor_id'])->count();

                if ($check === 0) {
                    $saveSponsor = EventSponsor::create($inputSponsor);
                }
            }
        }

        return $this->updateCompetitionActivity($request, $eventOld);
    }

    protected function updateCompetitionActivity(Request $request, $eventOld)
    {
        $arrCompetitionActivitiesNew = json_decode($request->competition_activity, true);
        $arrCompetitionActivitiesOld = json_decode($eventOld[0]->competition_activity, true);

        $eventMember = EventMember::select(
            'id'
        )
            ->where('event_id', $request->id)
            ->get();

        $eventJudge = EventJudge::select(
            'id'
        )
            ->where('event_id', $request->id)
            ->get();

        $arrEventMember = [];
        $arrEventJudge = [];

        foreach ($eventMember as $object) {
            $arrEventMember[] = $object->toArray();
        }

        foreach ($eventJudge as $object) {
            $arrEventJudge[] = $object->toArray();
        }

        $arrOldId = [];
        $arrNewId = [];
        $arrRemoveId = [];

        if ($arrCompetitionActivitiesOld !== null) {
            for ($i = 0; $i < sizeof($arrCompetitionActivitiesOld); $i++) {
                $oldId = $arrCompetitionActivitiesOld[$i]['id'];
                array_push($arrOldId, $oldId);
            }
        }

        if ($arrCompetitionActivitiesNew !== null) {
            for ($i = 0; $i < sizeof($arrCompetitionActivitiesNew); $i++) {
                $newId = $arrCompetitionActivitiesNew[$i]['id'];
                array_push($arrNewId, $newId);
            }
        }

        for ($i = 0; $i < sizeof($arrOldId); $i++) {
            if (!in_array($arrOldId[$i], $arrNewId)) {
                array_push($arrRemoveId, $arrOldId[$i]);
            }
        }

        Schema::disableForeignKeyConstraints();

        for ($i = 0; $i < sizeof($arrRemoveId); $i++) {
            $competitionActivityId = $arrRemoveId[$i];

            for ($j = 0; $j < sizeof($arrEventMember); $j++) {
                $deleteEventMemberAssignment = EventJudgeMemberAssignment::join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
                    ->where('event_member_classes.event_member_id', $arrEventMember[$i]['id'])
                    ->where('event_member_classes.competition_activity_id', $competitionActivityId)->delete();

                $deleteEventMemberClass = EventMemberClass::where('event_member_id', $arrEventMember[$i]['id'])
                    ->where('competition_activity_id', $competitionActivityId)->delete();
            }
            for ($j = 0; $j < sizeof($arrEventJudge); $j++) {
                $deleteEventJudgeAssignment = EventJudgeMemberAssignment::join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->where('event_judge_activities.event_judge_id', $arrEventJudge[$j]['id'])
                    ->where('event_judge_activities.competition_activity_id', $competitionActivityId)->delete();

                $deleteEventJudgeActivity = EventJudgeActivity::where('event_judge_id', $arrEventJudge[$j]['id'])
                    ->where('competition_activity_id', $competitionActivityId)->delete();
            }
        }

        Schema::enableForeignKeyConstraints();

        $activityOld = $eventOld[0]->competition_activity;

        if ($activityOld !== $request->competition_activity) {
            $update = Event::where('id', $request->id)->update(
                [
                    'competition_activity' => $request->competition_activity
                ]
            );

            $result = $this->storeEventActivityClassForm($request, $eventOld[0]);

            return response()->json(['status' => 'success', 'message' => 'updated successfully', 'res' => $result], 200);
        } else {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        }
    }

    protected function storeEventActivityClassForm($request, $event)
    {
        $arrActivityClass = json_decode($request->competition_activity, true);

        if ($arrActivityClass !== null) {

            $eventActivityClassForm = EventActivityClassForm::select()
                ->where('event_id', $event->id)
                ->get();

            $count = $eventActivityClassForm->count();

            $eventActivityClassFormArr = [];

            foreach ($eventActivityClassForm as $object) {
                $eventActivityClassFormArr[] = $object->toArray();
            }

            // UPDATE
            if ($count > 0) {

                $tobeDeleted = array_filter($eventActivityClassFormArr, function ($var) use ($arrActivityClass) {
                    for ($i = 0; $i < sizeof($arrActivityClass); $i++) {
                        if ($var['competition_activity_id'] === $arrActivityClass[$i]['id']) {
                            return false;
                        }
                    }
                    return true;
                });

                $tobeAdded = array_filter($arrActivityClass, function ($var) use ($eventActivityClassFormArr) {
                    for ($i = 0; $i < sizeof($eventActivityClassFormArr); $i++) {
                        if ($var['id'] === $eventActivityClassFormArr[$i]['competition_activity_id']) {
                            return false;
                        }
                    }
                    return true;
                });

                foreach ($tobeDeleted as $object) {
                    $delete = EventActivityClassForm::where('event_id', $event->id)
                        ->where('competition_activity_id', $object['competition_activity_id'])
                        ->where('class_grade_id', $object['class_grade_id'])
                        ->delete();
                }

                foreach ($tobeAdded as $object) {
                    $inputActivityClass['event_id'] = $event->id;
                    $inputActivityClass['competition_activity_id'] = $object['id'];
                    $inputActivityClass['form_generator_id'] = null;

                    $arrClassGradeIds = $object['class_grade_ids'];
                    for ($j = 0; $j < sizeof($arrClassGradeIds); $j++) {
                        // class grade id
                        $inputActivityClass['class_grade_id'] = $arrClassGradeIds[$j];
                        $saveEventActivityClassForm = EventActivityClassForm::create($inputActivityClass);
                    }
                }

                return ['tobeDeleted' => $tobeDeleted, 'tobeAdded' => $tobeAdded, 'eventActivityClassFormArr' => $eventActivityClassFormArr, 'arrActivityClass' => $arrActivityClass];
            }

            // ADD
            else {

                for ($i = 0; $i < sizeof($arrActivityClass); $i++) {
                    $inputActivityClass['event_id'] = $event->id;
                    $inputActivityClass['competition_activity_id'] = $arrActivityClass[$i]['id'];
                    $inputActivityClass['form_generator_id'] = null;

                    $arrClassGradeIds = $arrActivityClass[$i]['class_grade_ids'];
                    for ($j = 0; $j < sizeof($arrClassGradeIds); $j++) {
                        // class grade id
                        $inputActivityClass['class_grade_id'] = $arrClassGradeIds[$j];
                        $saveEventActivityClassForm = EventActivityClassForm::create($inputActivityClass);
                    }
                }
            }
        }
    }

    protected function storeClassGroups($request, $event)
    {
        $arrCustomClasses = json_decode($request->custom_classes, true);

        $input['event_id'] = $event->id;
        $input['class_country_id'] = $event->event_country_id;
        $input['association_id'] = $event->association_id;

        //for deleting
        $dontDeleteIds = [];
        if ($arrCustomClasses !== null) {
            for ($i = 0; $i < sizeof($arrCustomClasses); $i++) {

                $customClass = $arrCustomClasses[$i];

                $input['class_category_id'] = $event->getClassCategoryId($customClass);

                for ($j = 0; $j < sizeof($customClass['classes']); $j++) {
                    $classes = $customClass['classes'][$j];

                    $input['class_grade_id'] = $classes['class_id'];

                    for ($k = 0; $k < sizeof($classes['class_names']); $k++) {
                        $class_name = $classes['class_names'][$k];

                        if (array_key_exists('id', $class_name)) {
                            $classGroupUsed = EventMemberClass::where('class_group_id', $class_name['id'])->first();

                            if ($classGroupUsed) {
                                array_push($dontDeleteIds, $class_name['id']);
                                // array_splice($classes['class_names'], $k, 1);
                            }
                        }
                    }
                }
            }
        }

        $delete = ClassGroup::where('event_id', $event->id)->whereNotIn('id', $dontDeleteIds)->delete();

        //For storing
        if ($arrCustomClasses !== null) {
            for ($i = 0; $i < sizeof($arrCustomClasses); $i++) {

                $customClass = $arrCustomClasses[$i];

                $input['class_category_id'] = $event->getClassCategoryId($customClass);

                for ($j = 0; $j < sizeof($customClass['classes']); $j++) {
                    $classes = $customClass['classes'][$j];

                    $input['class_grade_id'] = $classes['class_id'];

                    for ($k = 0; $k < sizeof($classes['class_names']); $k++) {
                        $class_name = $classes['class_names'][$k];

                        if (array_key_exists('id', $class_name)) {
                            $classGroupUsed = EventMemberClass::where('class_group_id', $class_name['id'])->first();

                            if (!$classGroupUsed) {

                                $input['name'] = $class_name['name'];

                                $save = ClassGroup::create($input);
                            }
                        } else {

                            $input['name'] = $class_name['name'];

                            $save = ClassGroup::create($input);
                        }
                        // for ($l=0; $l < sizeof($dontDeleteIds); $l++) {
                        //     if ($dontDeleteIds[$l] == )
                        // }

                        // if (!array_key_exists('id', $class_name)) {

                        // }

                        // $classGroups = ClassGroup::where('class_grade_id', $input['class_grade_id'])->where('class_category_id', $input['class_category_id'])->where('event_id', $event->id)->get();

                        // foreach ($classGroups as $classGroup) {

                        //     if (strtolower($classGroup->name) != strtolower($input['name'])){
                        //     }
                        // }
                    }
                }
            }
        }

        return $input;
    }

    public function updateRecap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recap' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $event = Event::where('id', $request->id)->first();

        if ($event) {
            $eventUpdate['recap'] = $request->recap;

            $update = Event::where('id', $request->id)->update(
                [
                    'recap' => $eventUpdate['recap']
                ]
            );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 401);
        }
    }

    public function delete(Request $request, Event $event)
    {
        $eventMember = EventMember::select('id', 'event_id')
            ->where('event_id', $request->id)
            ->get();

        $eventJudge = EventJudge::select('id', 'event_id')
            ->where('event_id', $request->id)
            ->get();

        $eventSponsor = EventSponsor::select('id', 'event_id')
            ->where('event_id', $request->id)
            ->get();

        $eventClassGroup = ClassGroup::select('id', 'event_id')
            ->where('event_id', $request->id)
            ->get();

        $eventActivityClassForm = EventActivityClassForm::select('event_id')
            ->where('event_id', $request->id)
            ->get();


        $event = Event::select('id', 'banner')
            ->where('id', $request->id)
            ->get();

        $countEventMember = $eventMember->count();
        $countEventJudge = $eventJudge->count();
        $countEventSponsor = $eventSponsor->count();

        if ($countEventMember > 0) {
            return response()->json(['status' => 'failed', 'message' => 'There are participating members in this event, please remove them before deleting'], 200);
        }

        if ($countEventJudge > 0) {
            return response()->json(['status' => 'failed', 'message' => 'There are judges in this event, please remove them before deleting'], 200);
        }

        if ($countEventSponsor > 0) {
            $delete = EventSponsor::where('event_id', $request->id)->delete();

            // if ($delete) {
            //     return $this->deleteEvent($request, $event);
            // } else {
            //     $success = ['status' => 'failed', 'message' => 'delete failed'];
            //     return response()->json($success, $this->successStatus);
            // }
        }

        if ($eventClassGroup) {
            $delete = ClassGroup::where('event_id', $request->id)->delete();
        }

        if ($eventActivityClassForm) {
            $delete = EventActivityClassForm::where('event_id', $request->id)->delete();
        }

        return $this->deleteEvent($request, $event);
    }

    protected function deleteEvent(Request $request, $event)
    {
        $storagePath = public_path('upload/files/');
        $path = parse_url($event[0]->banner, PHP_URL_PATH);
        $file_name = basename($path);
        $fileUploadLink = $storagePath . 'event-banner/' . $file_name;

        if ($event[0]->banner !== '' && $event[0]->banner !== null) {
            if (file_exists($fileUploadLink)) {
                unlink($fileUploadLink);
            }
        }

        $delete = Event::where('id', $request->id)->delete();
        if ($delete) {
            $success = ['status' => 'success', 'message' => 'deleted successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'delete failed'];
        }

        return response()->json($success, $this->successStatus);
    }

    public function getEventVPMultiplier()
    {
        $vp_multiplier = VictoryPointMultiplier::all();

        $arr = [];

        foreach ($vp_multiplier as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    public function getEventYears()
    {
        $years = Event::selectRaw('YEAR(date_time_start) as year')
            ->whereRaw('YEAR(date_time_start) >= ?', [2019])
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year'); // Extracting only the 'year' values

        return response()->json($years);
    }
}
