Hello <i>{{ $invite->receiver_name }}</i>,
<p>You got invitation from {{ $invite->sender_name }} to access Fixle Planner</p>
 

<div>
<br>
<p><b>This is the invite link</b>&nbsp; <a href="{{ $invite->linkInvite }}">{{ $invite->linkInvite }}</a></p>
</div>

Thank You,
<br/>
<i>{{ $invite->sender }}</i>