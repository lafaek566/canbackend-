<h1><strong>Hi {{$data->receiverName}}, </strong></h1>
<br>
<div>
    <p>We received a request to reset your password for your CAN account: {{$data->receiverMail}}. We are here to help!</p>
    <p>Simply click on the link to set a new password:</p>
</div>


<p><a href=" {{ $data->link }}">{{ $data->link }}</a></p>

<br>
<div>
    <p>This link will be expired on <b>{{ $data->time }}</b>.</p>
</div>
<br>
<div>
    <p>If you didn't ask to change your password, don't worry! Your password is still save and you can delete this email.</p>
</div>

Thank You,
<br />
{{$data->sender}}
<i></i>