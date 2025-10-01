<?php
namespace App\Http\Controllers;
 
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Mail\inviteMail;
use App\User;
use App\FoInvite;
use App\FoProjects;
use App\FoProjectMember;
use App\Models\Account\FoAccount;
use App\Services\QueryAllHelper;
use App\Mail\ConfirmationMail;

class MailController extends Controller
{
    
    //get invitation email that still pending
    public function index(FoAccount $foAccount)
    {
        $user = Auth::user();
        $userId = $user->id;

        $foInvite = FoInvite::where('user_id',$userId)
                    ->where('already_accept', '0')
                    ->get();     

        return response()->json([
            'status' => 'success',
            'data' =>$foInvite->toArray(),
        ]);                       
    }

    //send email and store sender data
    public function store(Request $request)
    {
        /*
        variabel{
            *invite
            user_id // ini id dari orang yang kirim invitation
            receiver_name
            receiver_mail
            already_accept
            invite_code 
        }
        */
        $user = Auth::user();         
        $userId = $user->id;;

        //1. Check if the receiver email already a member of this account
        $foAccount = FoAccount::where('id',$request->account_id)
                    ->first();

        $checkUserInvite = User::where('email',$request->receiver_mail)
                    ->where('slug',$foAccount->slug)
                    ->first();

        if($checkUserInvite !== null)
        {
            return response()->json([
                'status' => 'failed',
                'data' =>$checkUserInvite,
                'extra' =>'User already member of this team account',
            ]); 
        }

        //2. check if already have a invitation
        $foInvite = FoInvite::where('receiver_mail',$request->receiver_mail)
                    ->where('user_id',$userId)
                    ->where('account_id',$foAccount->id)
                    ->first();



        $dataReceive = $request->all();

        $token = str_random(60); 
        $dataReceive['user_id'] = $userId;
        $dataReceive['already_accept'] = 0;
        $dataReceive['invite_code'] = $token;
        
        if($foInvite === null)
        {
            $foInvite = FoInvite::create($dataReceive);
        }else{
            $foInvite->update($dataReceive);
        }        
        $this->sendMail($token, $user, $foInvite);
        /*
        $objDemo = new \stdClass();
    

        $objDemo->sender = 'Fixle Organizer';
        $dataLink = 'https://www.fixleplanner.com.au/invites/' . $foInvite->user_id . '/' . $token;
        $objDemo->sender_name = $user->name;
        $objDemo->linkInvite = $dataLink;
        $objDemo->receiver_name = $foInvite->receiver_name;
 
        Mail::to($foInvite->receiver_mail)->send(new inviteMail($objDemo));

        
        // return response()->json($foInvite, 201);
        */
        return response()->json([
            'status' => 'success',
            'data' =>$foInvite->toArray(),
            'account' =>QueryAllHelper::instance()->getAllAccountById($request->account_id)
        ]); 
    }

    // Resend mail
    public function update(Request $request, FoAccount $foAccount, FoInvite $foInvite)
    {
        $user = Auth::user();         
        $userId = $user->id;;


        $dataReceive = $request->all();

        $token = str_random(60); 
        $dataReceive['already_accept'] = 0;
        $dataReceive['invite_code'] = $token;
 

        $foInvite->update($dataReceive);
        $this->sendMail($token, $user, $foInvite);
        /*
        $objDemo = new \stdClass();
  
        $objDemo->sender = 'Fixle Organizer';
        $dataLink = 'https://www.fixleplanner.com.au/invites/' . $foInvite->user_id . '/' . $token;
        $objDemo->sender_name = $user->name;
        $objDemo->linkInvite = $dataLink;
        $objDemo->receiver_name = $foInvite->receiver_name;
 
        Mail::to($foInvite->receiver_mail)->send(new inviteMail($objDemo));

        */
        return response()->json($foInvite, 201);
    }
   
    public function delete(FoAccount $foAccount, FoInvite $foInvite)
    {
        $accoundId=$foAccount->id;
        $foInvite->delete();

        return QueryAllHelper::instance()->getAllAccountById($accoundId);
    }




    public function check(Request $request,User $user)
    {
        /*
        variabel{
            *invite
            receiver_mail
            invite_code
            owner_id // ini id dari orang yang kirim invitation, nilainya ada di link

        }
        */
        
        //1. check apakah bener2 ada invitation
        $foInvite = FoInvite::where('invite_code',$request->invite_code)
                    ->first();
      
        $foAccount = FoAccount::where('id',$foInvite->account_id)
                    ->first();

        if($foInvite === null)
        {
            return response()->json([
                'status' => 'failed',
                'data' =>$foInvite->toArray(),
                'account' =>$foAccount->toArray(),
                'extra' =>'invitation ini tidak di temukan, arahkan saja ke HomePage',
            ]); 
        }


        //2. check apakah sudah pernah jadi user
        // $AlreadyUser = User::where('email',$foInvite->receiver_mail)->first();
        $AlreadyUser = User::where('email',$foInvite->receiver_mail)->where('slug',$foAccount->slug)->first();
        if($AlreadyUser === null)
        {
            return response()->json([
                'status' => 'user is empty',
                'data' =>$foInvite->toArray(),
                'account' =>$foAccount->toArray(),
                'extra' =>'user ini belum pernah buat account, alihkan ke page REGISTRASI dan bikin jadi member dari project yang di kirim oleh ownernya',
            ]);     
        }
/*
        //3. Ambil data project dari owner id
        $foProjects = FoAccount::where('owner_id',$request->owner_id)->first();
        $foProjectMember = FoProjectMember::where('member_id',$AlreadyUser->id)->first();

        if($foProjectMember)
        {
            return response()->json([
                'status' => 'user exist, not member',
                'data' =>$AlreadyUser->toArray(),
                'account' =>$foAccount->toArray(),
                'extra' =>'user ini sudah pernah buat account, tapi belum menjadi member projectnya, alihkan ke page LOGIN dan bikin jadi member dari project yang di kirim oleh ownernya',
            ]); 
        }
            
*/
        return response()->json([
            'status' => 'user exist, already member',
            'data' =>$AlreadyUser->toArray(),
            'account' =>$foAccount->toArray(),
            'extra' =>'user ini sudah pernah buat account, dan sudah menjadi member projectnya, maka alihkan ke LOGIN  saja',
        ]); 
        
    }

    public function test() 
    {
        // $this->sendEmail('', '', 'latureolaini@gmail.com');
        
        // return response()->json('test', 201);
    }

    public function sendMail($confirmCode, $receiver)
    {
        $objDemo = new \stdClass();
        $objDemo->sender = 'CAN Organizer';
        $objDemo->confirmCode = $confirmCode;
 
        Mail::to($receiver)->send(new ConfirmationMail($objDemo));
    }
}