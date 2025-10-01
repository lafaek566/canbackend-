<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Assessment</title>


    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }


        .avatar {
            vertical-align: middle;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>

</head>

<body>
    <h2>{{$data->event_data['title']}}</h2>

    <table style="width:100%">
        <tr>
            <th>Event Details:</th>
            <th>Competition Details:</th>
        </tr>
        <tr>
            <td style="width: 50vw">
                <div>Location: <i>{{$data->event_data['location']}}</i></div>
                <div>Country: <i>{{$data->event_data['event_countries_name']}}</i></div>
                <div>Association: <i>{{$data->event_data['associations_name']}}</i></div>
                <div>Country Type: <i>{{$data->event_data['event_country_name']}}</i></div>
                <div>Type: <i>{{$data->event_data['event_type_name']}}</i></div>
            </td>
            <td style="width: 50vw; vertical-align: top">
                <div>Activity: <i>{{$data->participant_data['competition_activity_name']}}</i></div>
                <div>Class Grade: <i>{{$data->participant_data['class_grade_name']}}</i></div>
                <div>Class Group: <i>{{$data->participant_data['class_group_name']}}</i></div>
                <div></div>
            </td>
        </tr>
    </table>

    <hr>

    <table style="width:100%;">
        <tr>
            <td style="width: 5vw">
                <?php if (array_key_exists('avatar', $data->judge_data) && $data->judge_data['avatar']) : ?>
                    <img src="{{$message->embed($data->judge_data['avatar'])}}" alt="Avatar" class="avatar">
                <?php else : ?>
                    <img src="{{$message->embed('https://www.w3schools.com/howto/img_avatar.png')}}" alt="Avatar" class="avatar">
                <?php endif; ?>
            </td>
            <td style="width: 46.5vw">
                <div><b>Judge</b></div>
                <div>
                    {{$data->judge_data['name']}}
                </div>
            </td>
            <td style="width: 45vw; text-align: right;">
                <div><b>Participant</b></div>
                <div>
                    {{$data->participant_data['member_name']}}
                </div>
            </td>
            <td style="width: 5vw">
                <?php if ($data->participant_data['member_avatar']) : ?>
                    <img src="{{$message->embed($data->participant_data['member_avatar'])}}" alt="Avatar" class="avatar">
                <?php else : ?>
                    <img src="{{$message->embed('https://www.w3schools.com/howto/img_avatar2.png')}}" alt="Avatar" class="avatar">
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <table style="width:100%">
        <tr>
            <th>Total Score:</th>
            <th>Car Details:</th>
        </tr>
        <tr>
            <td style="width: 50vw">
                <h2>{{$data->form_assessment['grandTotal']}}</h2>
            </td>
            <td style="width: 50vw; vertical-align: top">
                <div>Vehicle: <i>{{$data->participant_car['vehicle']}}</i></div>
                <div>License Plate: <i>{{$data->participant_car['license_plate']}}</i></div>
                <div>VIN: <i>{{$data->participant_car['vin_number']}}</i></div>
                <div>Type: <i>{{$data->participant_car['type']}}</i></div>
                <div>Color: <i>{{$data->participant_car['color']}}</i></div>
            </td>
        </tr>
    </table>

    <hr>

    <table style="width:100%">
        <tr>
            <th>Scoring Details:</th>
        </tr>
        <tr>
            <td>{{$data->event_data['date_start']}}</td>
        </tr>
    </table>

    <table style="width:100%">
        <?php foreach ($data->form_assessment['sections'] as $key1 => $value) : ?>

            <tr>
                <th colspan="3"><?php echo $data->form_assessment['sections'][$key1]['sectionName']; ?></th>
            </tr>

            <?php foreach ($data->form_assessment['sections'][$key1]['questions'] as $key2 => $value) : ?>

                <?php if ($data->form_assessment['sections'][$key1]['questions'][$key2]['_type'] == 'qt1') : ?>

                    <tr>
                        <td></td>
                        <td>
                            <b><?php echo $key2 + 1 . '. ' . $data->form_assessment['sections'][$key1]['questions'][$key2]['title'] ?></b>
                            <div>
                                <?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selected']['label'] ?>
                            </div>
                        </td>
                        <td><b><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['score'] ?></b></td>
                    </tr>

                <?php elseif ($data->form_assessment['sections'][$key1]['questions'][$key2]['_type'] == 'qt2') : ?>

                    <tr>
                        <td></td>
                        <td>
                            <b><?php echo $key2 + 1 . '. ' . $data->form_assessment['sections'][$key1]['questions'][$key2]['title'] ?></b>
                            <div>
                                <?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selected']['label'] ?>
                                (<?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['label'] ?>)
                            </div>
                        </td>
                        <td>
                            <b><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['value'] ?></b>
                        </td>
                    </tr>

                <?php elseif ($data->form_assessment['sections'][$key1]['questions'][$key2]['_type'] == 'qt3') : ?>

                    <tr>
                        <td></td>
                        <td>
                            <b><?php echo $key2 + 1 . '. ' . $data->form_assessment['sections'][$key1]['questions'][$key2]['title'] ?></b>
                            <div>
                                (<?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['label'] ?>)
                            </div>
                        </td>
                        <td>
                            <b><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['value'] ?></b>
                        </td>
                    </tr>

                <?php elseif ($data->form_assessment['sections'][$key1]['questions'][$key2]['_type'] == 'qt4') : ?>

                    <tr>
                        <td></td>
                        <td>
                            <b><?php echo $key2 + 1 . '. ' . $data->form_assessment['sections'][$key1]['questions'][$key2]['title'] ?></b>
                            <i><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['subTitle'] ?></i>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td rowspan="2"></td>
                        <td>

                            <?php foreach ($data->form_assessment['sections'][$key1]['questions'][$key2]['options'] as $key3 => $value) : ?>

                                <?php if (array_key_exists('checked', $data->form_assessment['sections'][$key1]['questions'][$key2]['options'][$key3])) : ?>

                                    <div>
                                        <?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['options'][$key3]['label'] ?>
                                    </div>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </td>
                        <td rowspan="2">

                            <?php foreach ($data->form_assessment['sections'][$key1]['questions'][$key2]['options'] as $key3 => $value) : ?>

                                <?php if (array_key_exists('checked', $data->form_assessment['sections'][$key1]['questions'][$key2]['options'][$key3])) : ?>

                                    <div>
                                        <b><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['options'][$key3]['value'] ?></b>
                                    </div>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </td>
                    </tr>
                    <tr>

                    </tr>

                <?php elseif ($data->form_assessment['sections'][$key1]['questions'][$key2]['_type'] == 'qt5') : ?>

                    <tr>
                        <td></td>
                        <td>
                            <b><?php echo $key2 + 1 . '. ' . $data->form_assessment['sections'][$key1]['questions'][$key2]['title'] ?></b>
                            <i><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['subTitle'] ?></i>
                        </td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>

                            <?php foreach ($data->form_assessment['sections'][$key1]['questions'][$key2]['groups'] as $key3 => $value) : ?>

                                <?php if (array_key_exists('selectedScore', $data->form_assessment['sections'][$key1]['questions'][$key2]['groups'][$key3])) : ?>

                                    <div>
                                        <b><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['groups'][$key3]['label'] ?></b>
                                        (<?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['groups'][$key3]['selectedScore']['label'] ?>)
                                    </div>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </td>
                        <td>

                            <?php foreach ($data->form_assessment['sections'][$key1]['questions'][$key2]['groups'] as $key3 => $value) : ?>

                                <?php if (array_key_exists('selectedScore', $data->form_assessment['sections'][$key1]['questions'][$key2]['groups'][$key3])) : ?>

                                    <div>
                                        <b><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['groups'][$key3]['selectedScore']['value'] ?></b>
                                    </div>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </td>
                    </tr>

                <?php elseif ($data->form_assessment['sections'][$key1]['questions'][$key2]['_type'] == 'qt6') : ?>

                    <tr>
                        <td></td>
                        <td>
                            <b><?php echo $key2 + 1 . '. ' . $data->form_assessment['sections'][$key1]['questions'][$key2]['title'] ?></b>
                            <div>
                                (<?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selected']['label'] ?>)
                            </div>
                            <div>
                                <?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedNestedOption']['label'] ?>
                            </div>
                        </td>
                        <td>
                            <b><?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['value'] ?></b>
                        </td>
                    </tr>
                <?php elseif ($data->form_assessment['sections'][$key1]['questions'][$key2]['_type'] == 'qt7') : ?>

                    <tr>
                        <td></td>
                        <td>
                            <b><?php echo $key2 + 1 . '. ' . $data->form_assessment['sections'][$key1]['questions'][$key2]['title'] ?></b>
                            <div>
                                (<?php echo $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['label'] ? $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['label'] . "|" . $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['value'] : $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['value']; ?>)
                            </div>
                        </td>
                        <td>
                            <b><?php $value = $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['value'];
                                $multiplier = $data->form_assessment['sections'][$key1]['questions'][$key2]['selectedScore']['multiplier'];

                                echo $value * $multiplier ?></b>
                        </td>
                    </tr>

                <?php else : ?>
                    <p>Question type not available</p>
                <?php endif; ?>

            <?php endforeach; ?>

            <tr>
                <td></td>
                <th>Sub Total</th>
                <td><b><?php echo $data->form_assessment['sections'][$key1]['sectionScore']; ?></b></td>
            </tr>

        <?php endforeach; ?>

        <tr>
            <td colspan="2">
                <h3>Grand Total</h3>
            </td>
            <td>
                <h2>{{$data->form_assessment['grandTotal']}}</h2>
            </td>
        </tr>
    </table>

    <hr>

    <table style="width:100%; max-width: 100%;">
        <tr>
            <td style="width: 5vw">
                <?php if (array_key_exists('avatar', $data->judge_data) && $data->judge_data['avatar']) : ?>
                    <img src="{{$message->embed($data->judge_data['avatar'])}}" alt="Avatar" class="avatar">
                <?php else : ?>
                    <img src="{{$message->embed('https://www.w3schools.com/howto/img_avatar.png')}}" alt="Avatar" class="avatar">
                <?php endif; ?>
            </td>
            <td style="width: 45vw">
                <div><b>Judge</b></div>
                <div>
                    {{$data->judge_data['name']}}
                </div>
            </td>
            <td style="width: 45vw; text-align: right;">
                <div><b>Participant</b></div>
                <div>
                    {{$data->participant_data['member_name']}}
                </div>
            </td>
            <td style="width: 5vw">
                <?php if ($data->participant_data['member_avatar']) : ?>
                    <img src="{{$message->embed($data->participant_data['member_avatar'])}}" alt="Avatar" class="avatar">
                <?php else : ?>
                    <img src="{{$message->embed('https://www.w3schools.com/howto/img_avatar2.png')}}" alt="Avatar" class="avatar">
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div>
                    <img src="{{$message->embed($data->judge_signature)}}" height="30%">
                </div>
            </td>
            <td colspan="2" style="text-align: right;">
                <div>
                    <img src="{{$message->embed($data->participant_signature)}}" height="30%">
                </div>
            </td>
        </tr>
    </table>

    <hr>

    Thank You,
    <br />
    {{$data->sender}}
    <i></i>
</body>

</html>
