<?php

namespace App\Http\Controllers;

use App\ClassGroup;
use App\EventJudgeMemberAssignment;
use App\EventMember;
use Illuminate\Http\Request;
use App\EventMemberClass;
use App\FormGenerator;
use App\Mail\AssessmentResults;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventMemberClassController extends Controller
{
    public $successStatus = 200;

    public function getParticipantPointEachEventMemberClass(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'events.id AS event_id',
            'events.title AS event_title',
            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name',
            'class_groups.id AS class_group_id',
            'class_groups.name AS class_group_name',
            'event_member_classes.studio_info AS studio_info',
            'event_member_classes.gear AS gear',
            'event_member_classes.team_name AS team_name',
            'event_member_classes.victory_point AS victory_point',
            'cars.id AS car_id',
            'cars.avatar AS car_avatar',
            'cars.vehicle AS car_vehicle'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->leftJoin('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->whereNotNull('event_judge_member_assignments.id')
            ->where('event_members.member_id', '=', $request->member_id)
            ->where('events.status_score_final', '=', 1)
            ->where('competition_activities.id', '=', 1)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventMemberClassCount = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'events.id AS event_id',
            'events.title AS event_title',
            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name',
            'class_groups.id AS class_group_id',
            'class_groups.name AS class_group_name',
            'event_member_classes.studio_info AS studio_info',
            'event_member_classes.gear AS gear',
            'event_member_classes.team_name AS team_name',
            'event_member_classes.victory_point AS victory_point',
            'cars.id AS car_id',
            'cars.avatar AS car_avatar',
            'cars.vehicle AS car_vehicle'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->leftJoin('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->whereNotNull('event_judge_member_assignments.id')
            ->where('event_members.member_id', '=', $request->member_id)
            ->where('events.status_score_final', '=', 1)
            ->where('competition_activities.id', '=', 1)
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $eventMemberClassCount]);
    }

    public function getParticipantFromClassGroup(Request $request, ClassGroup $classGroup)
    {
        $datas = $classGroup->eventMemberClass()
            ->whereHas("eventMember", function ($query) use ($request) {
                $query->where("event_id", "=", $request->event_id);
            })
            ->with(
                [
                    "eventMember",
                ]
            )
            ->get();

        foreach ($datas as $data) {
            $eventJudgeMemberAssignment = new EventJudgeMemberAssignment();
            $judge = $eventJudgeMemberAssignment->getJudgeAssignToParticipant(
                $data['id']
            );
            $cars = $data->car;
            $data["judge"] = count($judge) > 0 ? $judge[0] : [];
            $data["vehicle"] = $cars["vehicle"];
            $data["type"] = $cars["type"];
            $data["vin_number"] = $cars["vin_number"];
            $data["name"] = $cars->user->name;
            $data["score"] = $data["grand_total"];
            $data["member_id"] = $data->eventMember->member_id;
        }

        $total = $classGroup->eventMemberClass()
            ->whereHas("eventMember", function ($query) use ($request) {
                $query->where("event_id", "=", $request->event_id);
            })
            ->with(
                [
                    "eventMember"
                ]
            )
            ->count();

        return response()->json(
            [
                "data" => $datas,
                "total" => $total
            ],
            $this->successStatus
        );
    }

    public function getParticipantDisqualifiedStatus(Request $request)
    {
        $eventMemberClass = EventMemberClass::where('id', $request->event_member_class_id)->first();

        if ($eventMemberClass) {

            if ($eventMemberClass->disqualified_status === 0) {
                return response()->json(['status' => 'success', 'disqualified_status' => false], $this->successStatus);
            } else {
                return response()->json(['status' => 'success', 'disqualified_status' => true], $this->successStatus);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'class of participant not found'], 200);
        }
    }

    public function disqualifyParticipantClass(Request $request)
    {
        $eventMemberClass = EventMemberClass::where('id', $request->event_member_class_id)->first();

        if ($eventMemberClass) {
            $update = EventMemberClass::where('id', $request->event_member_class_id)->update(
                [
                    'disqualified_status' => $request->disqualified_status
                ]
            );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'updated successfully'], $this->successStatus);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'class of participant not found'], 200);
        }
    }

    public function deleteParticipantListClass(Request $request)
    {
        $eventMemberClass = EventMemberClass::where('id', $request->event_member_class_id)->first();


        // $deleteEventJudgeMemberAssignment = EventJudgeMemberAssignment::join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
        // ->where('event_member_classes.event_member_id', $request->event_member_id)->delete();
        if ($request->event_judge_member_assignment_id !== null) {
            $deleteEventJudgeMemberAssignment = EventJudgeMemberAssignment::where('id', $request->event_judge_member_assignment_id)->delete();
        }

        if ($eventMemberClass) {
            $delete = EventMemberClass::where('id', $request->event_member_class_id)->delete();

            if ($delete) {

                $eventMemberClassCount = EventMemberClass::where('event_member_id', $request->event_member_id)->count();

                if ($eventMemberClassCount <= 0) {
                    $delete = EventMember::where('id', $request->event_member_id)->delete();

                    if ($delete) {
                        return response()->json(['status' => 'success', 'message' => 'delete successfully'], $this->successStatus);
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'delete event member failed', 'eventMemberClassCount' => $eventMemberClassCount, 'event_member_id' => $request->event_member_id], 200);
                    }
                } else {
                    return response()->json(['status' => 'success', 'message' => 'delete successfully', 'eventMemberClassCount' => $eventMemberClassCount, 'event_member_id' => $request->event_member_id], $this->successStatus);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'delete failed'], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'class of participant not found'], 200);
        }
    }

    public function storeAssessment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'event_member_class_id' => 'required',
            'form_assessment' => 'required',
            'grand_total' => 'required',
            'judge_signature' => 'required',
            'participant_signature' => 'required',
            'event_data' => 'required',
            'judge_data' => 'required',
            'participant_data' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }


        $eventMemberClass = EventMemberClass::where('id', $request->event_member_class_id)->first();

        if ($eventMemberClass) {

            $host = \Config::get('project-config.project_host');
            $protocol = \Config::get('project-config.project_protocol');

            // STORE IMAGE
            // Judge
            $base64_image = $request->judge_signature; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            $imageName = str_random(10) . '.' . 'png';
            $pathJudge = 'public/events/' . $request->event_member_class_id . '/' . $imageName;
            Storage::disk('local')->put($pathJudge, base64_decode($file_data));
            $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            $judgeUrl = Storage::url($pathJudge);
            $judgeImageUrl = $protocol . '://' . $host . 'storage/app/public/events/' . $request->event_member_class_id . '/' . $imageName;

            $input['judge_signature'] = $judgeImageUrl;

            // Participant
            $base64_image = $request->participant_signature; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            $imageName = str_random(10) . '.' . 'png';
            $pathParticipant = 'public/events/' . $request->event_member_class_id . '/' . $imageName;
            Storage::disk('local')->put($pathParticipant, base64_decode($file_data));
            $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            $participantUrl = Storage::url($pathParticipant);
            $participantImageUrl = $protocol . '://' . $host . 'storage/app/public/events/' . $request->event_member_class_id . '/' . $imageName;

            $input['participant_signature'] = $participantImageUrl;
            $input['storagePath'] = $storagePath;
            // $input['url'] = $url;

            $form_assessment = json_decode($request->form_assessment, true);
            $event_data = json_decode($request->event_data, true);
            $judge_data = json_decode($request->judge_data, true);
            $participant_data = json_decode($request->participant_data, true);

            $data['form_assessment'] = $form_assessment;
            $data['event_data'] = $event_data;
            $data['judge_data'] = $judge_data;
            $data['participant_data'] = $participant_data;
            $data['judge_signature'] = $storagePath . $pathJudge;
            $data['participant_signature'] = $storagePath . $pathParticipant;

            $this->sendAssessmentResultsMail($participant_data['member_email'], $data);

            $update = EventMemberClass::where('id', $request->event_member_class_id)->update(
                [
                    'form_assessment' => $request->form_assessment,
                    'grand_total' => $request->grand_total,
                    'judge_signature' => $judgeImageUrl,
                    'participant_signature' => $participantImageUrl
                ]
            );

            // return response()->json(['status' => 'failed', 'message' => 'class of participant not found'], 200);
            //  return response()->json(['status' => 'success', 'message' => 'updated successfully', 'data' =>  $participant_data], $this->successStatus);

            if ($update) {

                return response()->json(['status' => 'success', 'message' => 'updated successfully', 'res' => $input, 'data' =>  $participant_data], $this->successStatus);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'update failed', 'data' =>  $participant_data], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'class of participant not found'], 200);
        }
    }

    public function resetAssessment(EventMemberClass $eventMemberClass)
    {
        $update = $eventMemberClass->update(
            [
                "form_assessment" => null,
                "grand_total" => null,
                "judge_signature" => null,
                "participant_signature" => null,
            ]
        );

        if ($update) {
            return response()->json(
                [
                    "status" => "success",
                    "message" => "updated successfully"
                ],
                $this->successStatus
            );
        } else {
            return response()->json(
                [
                    "status" => "failed",
                    "message" => "updated failed",
                ],
                401
            );
        }
    }

    protected function sendAssessmentResultsMail($receiver, $data)
    {
        $objDemo = new \stdClass();
        $objDemo->sender = 'CAN Organizer';
        $objDemo->form_assessment = $data['form_assessment'];
        $objDemo->event_data = $data['event_data'];
        $objDemo->judge_data = $data['judge_data'];
        $objDemo->participant_data = $data['participant_data'];
        $objDemo->judge_signature = $data['judge_signature'];
        $objDemo->participant_signature = $data['participant_signature'];
        $event_member_class_id = $data['participant_data']["event_member_class_id"];
        $objDemo->participant_car = EventMemberClass::find($event_member_class_id)->car;


        $users_temp = explode(',', 'can2020logger@gmail.com');
        // $users_temp = [];
        array_push($users_temp, $receiver);
        array_push($users_temp, $data['judge_data']['email']);
        $users = [];
        foreach ($users_temp as $key => $ut) {
            $ua = [];
            $ua['email'] = $ut;
            //   $ua['name'] = 'test';
            $users[$key] = (object) $ua;
        }

        Mail::to($users)->send(new AssessmentResults($objDemo));

        if (count(Mail::failures()) > 0) {

            //    echo "There was one or more failures. They were: <br />";

            return response()->json(['status' => 'failed', 'message' => 'email failed'], 401);

            //    foreach(Mail::failures() as $email_address) {
            //        echo " - $email_address <br />";
            //     }

        } else {
            // echo "No errors, all sent successfully!";
        }
    }

    public function updateAssessment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_assessment' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }


        $eventMemberClass = EventMemberClass::where('id', $request->event_member_class_id)->first();

        if ($eventMemberClass) {

            if ($eventMemberClass->judge_signature == null && $eventMemberClass->participant_signature == null) {

                $update = EventMemberClass::where('id', $request->event_member_class_id)->update(
                    [
                        'form_assessment' => $request->form_assessment
                    ]
                );

                if ($update) {

                    return response()->json(['status' => 'success', 'message' => 'updated successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'assessment has already been signed'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'class of participant not found'], 200);
        }
    }

    public function randomAssessment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'forms' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $forms = json_decode($request->forms);

        if (sizeof($forms) > 1) {
            $random_form_key = array_rand($forms);
        } else {
            $random_form_key = 0;
        }

        // if ($random_form_key) {
        $formGenerator = FormGenerator::where('id', $forms[$random_form_key]->form_generator_id)->first();

        if ($formGenerator) {

            $eventMemberClass = EventMemberClass::where('id', $request->event_member_class_id)->first();

            if ($eventMemberClass) {

                $update = EventMemberClass::where('id', $request->event_member_class_id)->update(
                    [
                        'form_id' => $formGenerator->id,
                        'form_title' => $formGenerator->title,
                        'form_assessment' => $formGenerator->form_assessment
                    ]
                );

                if ($update) {
                    return response()->json(['status' => 'success', 'message' => 'updated successfully', 'data' => $formGenerator], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'update participant failed'], 401);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'class of participant not found'], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'failed to find form'], 200);
        }
        // } else {
        //     return response()->json(['status' => 'failed', 'message' => 'failed to randomize form'], 200);
        // }
    }

    public function finalize(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'event_member_class_id' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 401);
        // }

        $eventMemberClass = EventMemberClass::where('id', $request->event_member_class_id)->first();

        if ($eventMemberClass) {
            if ($eventMemberClass->grand_total != null) {

                $update = EventMemberClass::where('id', $request->event_member_class_id)->update(
                    [
                        'status_score' => 1
                    ]
                );

                if ($update) {
                    return response()->json(['status' => 'success', 'message' => 'score successfully finalized'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'failed to finalize score'], 401);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'please assess or give score to participant before finalizing'], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'class of participant not found'], 401);
        }
    }

    public function unfinalize(EventMemberClass $eventMemberClass)
    {
        $update = $eventMemberClass->update(
            [
                "status_score" => 0
            ]
        );

        if ($update) {
            return response()->json(
                [
                    "status" => "success",
                    "message" => "score successfully unfinalized"
                ],
                $this->successStatus
            );
        } else {
            return response()->json(
                [
                    "status" => "failed",
                    "message" => "failed to finalize score"
                ],
                401
            );
        }
    }

    public function massFinalize(Request $request)
    {
        if (count($request->eventMemberId) > 0) {
            $update = EventMemberClass::whereIn("id", $request->eventMemberId)
                ->update(
                    [
                        "status_score" => 1
                    ]
                );

            if ($update) {
                return response()->json(
                    [
                        "status" => "success",
                        "message" => "score successfully mass finalized"
                    ],
                    $this->successStatus
                );
            } else {
                return response()->json(
                    [
                        "status" => "failed",
                        "message" => "failed to finalize mass"
                    ],
                    401
                );
            }
        }
        return response()->json(
            [
                "status" => "failed",
                "message" => "event member id is not passed"
            ],
            401
        );
    }
}
