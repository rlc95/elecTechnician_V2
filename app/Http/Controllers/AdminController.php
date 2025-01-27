<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Seller;
use constGuards;
use constDefaults;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    //login
    public function loginHandler(Request $request){

        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if($fieldType == 'email'){
            $request->validate([
                'login_id' => 'required|email|exists:admins,email',
                'password' => 'required|min:6'
            ],[
                'login_id.required' => 'Email or Username is required',
                'login_id.email' => 'Invalid email address',
                'login_id.exists' => 'Email not found',
                'password.required' => 'Password is required',
            ]);
        }else{
            $request->validate([
                'login_id' => 'required|exists:admins,username',
                'password' => 'required|min:6'
            ],[
                'login_id.required' => 'Email or Username is required',
                'login_id.exists' => 'Username not found',
                'password.required' => 'Password is required',
            ]);
        }

        $credentials = [
            $fieldType => $request->login_id,
            'password' => $request->password
        ];

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.home');
        }else{
            session()->flash('fail','Invalid credentials');
            return redirect()->route('admin.login');
        }

    }

    //home page
    public function home(Request $request){
        $admin = null;
        if( Auth::guard('admin')->check() ){
            $admin = Admin::findOrFail(auth()->id());
        }

        $clientCount =  DB::table('clients')->count();
        $sellersCount =  DB::table('sellers')->count();
        $servicesCount =  DB::table('services')->count();
        $servicseRequestCount =  DB::table('service_requests')->count();

        $data = [
            'pageTitle'=>'Admin Home ',
            'admin'=>$admin,
            'clients' =>$clientCount,
            'sellers' =>$sellersCount,
            'services' => $servicesCount,
            'serviceRequests' => $servicseRequestCount
        ];

        return view('back.pages.admin.home', $data);
    }

    //logout
    public function logoutHandler(Request $request){
        Auth::guard('admin')->logout();
        session()->flash('fail','You are logged out');
        return redirect()->route('admin.login');
    }

    //send password reset link
    public function sendPasswordResetLink(Request $request){
        $request->validate([
            'email' => 'required|email|exists:admins,email'
        ],[
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email address',
            'email.exists' => 'Email not found',
        ]);

        //get admin details
        $admin = Admin::where('email',$request->email)->first();

        //generate token
        $token = base64_encode(Str::random(64));

        //check if there is an exsisting reset password token
        $oldToken = DB::table('password_resets')
                    ->where('email', $request->email)
                    ->where('guard', constGuards::ADMIN)
                    ->first();
        // dd($oldToken);
        if($oldToken){
            //update the token
            DB::table('password_resets')
                ->where('email', $request->email)
                ->where('guard', constGuards::ADMIN)
                ->update([
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
        }else{
            //insert the token
            DB::table('password_resets')
            ->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
                'guard' => constGuards::ADMIN
            ]);
        }

        $actionLink = route('admin.reset-password',['token'=>$token,'email'=>$request->email]);

        $data = array(
            'actionLink' => $actionLink,
            'admin' => $admin
        );

        $mail_body = view('email-templates.admin-forgot-email-template',$data)->render();

        $mailConfig = array(
            'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
            'mail_from_name' => env('EMAIL_FROM_NAME'),
            'mail_recipient_email' => $admin->email,
            'mail_recipient_name' => $admin->name,
            'mail_subject' => 'Reset Password',
            'mail_body' => $mail_body
        );

        if(sendEmail($mailConfig)){
            session()->flash('success','Password reset link sent to your email');
            return redirect()->route('admin.forgot-password');
        }else{
            session()->flash('fail','Something went wrong');
            return redirect()->route('admin.forgot-password');
        }
    }

    //reset password
    public function resetPassword(Request $request, $token = null){
        $checkToken = DB::table('password_resets')
                    ->where('token', $request->token)
                    ->where('guard', constGuards::ADMIN)
                    ->first();

        if($checkToken){
            //check if token is expired
            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s',$checkToken->created_at)->diffInMinutes(Carbon::now());

            if($diffMins > constDefaults::tokenExpiredMinutes){
                session()->flash('fail','Token expired, request another link');
                return redirect()->route('admin.forgot-password',['token' => $token]);
            }else{
                return view('back.pages.admin.auth.reset-password')->with(['token' => $token]);

            }

        }else{
            session()->flash('fail','Invalid token, request another link');
            return redirect()->route('admin.forgot-password',['token' => $token]);
        }

    }

    public function resetPasswordHandler(Request $request){
        $request->validate(([
            'new_password' => 'required|min:5|max:45|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]));

        $token = DB::table('password_resets')
                    ->where('token', $request->token)
                    ->where('guard', constGuards::ADMIN)
                    ->first();

        //get admin details
        $admin = Admin::where('email',$token->email)->first();

        //update password
        Admin::where('email',$token->email)->update([
            'password' => Hash::make($request->new_password)
        ]);

        //delete token
        DB::table('password_resets')
            ->where('email', $admin->email)
            ->where('token', $request->token)
            ->where('guard', constGuards::ADMIN)
            ->delete();

        //send email
        $data = array(
            'admin' => $admin,
            'new_password' => $request->new_password
        );

        $mail_body = view('email-templates.admin-reset-email-template',$data)->render();

        $mailConfig = array(
            'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
            'mail_from_name' => env('EMAIL_FROM_NAME'),
            'mail_recipient_email' => $admin->email,
            'mail_recipient_name' => $admin->name,
            'mail_subject' => 'Password Reset Successful',
            'mail_body' => $mail_body
        );

        sendEmail($mailConfig);
        return redirect()->route('admin.login')->with('success','Done! Password reset successful. USe new password to login system');
    }

    //profile view
    public function profileView(Request $request){
        $admin = null;
        if( Auth::guard('admin')->check() ){
            $admin = Admin::findOrFail(auth()->id());
        }
        return view('back.pages.admin.profile', compact('admin'));
    }

    //update profile
    public function updateProfile(Request $request){ //dd($request);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
        ]);

        $admin = Admin::findOrFail(auth()->id()); //dd($admin);
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->username = $request->username;
        $admin->save();

        // $this->showToastr('success','Your personal details have been successfully updated.');

        return redirect()->route('admin.profile')->with('success','Profile updated successfully');
    }

    public function showToastr($type, $message){
        return $this->dispatch('showToastr',[
             'type'=>$type,
             'message'=>$message
        ]);
    }

    //change profile picture
    public function changeProfilePicture(Request $request){


        $admin = Admin::findOrFail(auth()->id());
        $path = 'images/users/admins/';

        if($request->hasFile('adminProfilePictureFile')){
            $file = $request->file('adminProfilePictureFile');
            $old_picture = $admin->getAttributes()['picture'];
            $file_path = $path.$old_picture;
            $filename = 'ADMIN_IMG_'.rand(2,1000).$admin->id.time().uniqid().'.jpg';

            $upload = $file->move(public_path($path),$filename);
            if($upload){
                if( $old_picture != null && File::exists(public_path($path.$old_picture)) ){
                    File::delete(public_path($path.$old_picture));
                }
                $admin->update(['picture'=>$filename]);
                return response()->json(['status'=>1,'msg'=>'Your profile picture has been successfully updated.']);
            }else{
                return response()->json(['status'=>0,'msg'=>'Something went wrong.']);
            }
        }

        return redirect()->route('admin.profile')->with('success','Profile picture updated successfully');
    }

    //change password
    public function changePassword(Request $request){
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:5|max:45|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]);

        $admin = Admin::findOrFail(auth()->id());

        if(Hash::check($request->current_password, $admin->password)){
            $admin->update(['password'=>Hash::make($request->new_password)]);
            return redirect()->route('admin.profile')->with('success','Password updated successfully');
        }else{
            return redirect()->route('admin.profile')->with('fail','Old password is incorrect');
        }
    }

    //user view page
    public function users(Request $request){
        $admin = null;
        if( Auth::guard('admin')->check() ){
            $admin = Admin::findOrFail(auth()->id());
        }
        $client = DB::table('clients as a')->selectRaw('a.name, a.email, a.phone, a.address, b.name_en as province, c.name_en as district, d.name_en as city')
                ->leftJoin('provinces as b','b.id','=','a.provinces')
                ->leftJoin('districts as c','c.id','=','a.districts')
                ->leftJoin('cities as d','d.id','=','a.cities')
                ->get();

        $data = [
            'pageTitle'=>'Client Profile',
            'client'=>$client,
            'admin'=>$admin,
        ];

        return view('back.pages.admin.users', $data);
    }

    //registerd seller page
    public function sellers(Request $request){
        $admin = null;
        if( Auth::guard('admin')->check() ){
            $admin = Admin::findOrFail(auth()->id());
        }
        $sellers = DB::table('sellers as a')->selectRaw('a.name, a.email, a.phone, a.address, c.name_en as district, d.name_en as city, e.title  as service1, f.title  as service2, g.title  as service3, IF(a.status = 1, "Verifyed", IF(a.status = 2,"Pending","Not verified")) as status')
                ->leftJoin('districts as c','c.id','=','a.districts')
                ->leftJoin('cities as d','d.id','=','a.cities')
                ->leftJoin('services as e','e.id','=','a.service1')
                ->leftJoin('services as f','f.id','=','a.service2')
                ->leftJoin('services as g','g.id','=','a.service3')
                ->get();

        $data = [
            'pageTitle'=>'Registerd Sellers',
            'sellers'=>$sellers,
            'admin'=>$admin,
        ];

        return view('back.pages.admin.sellers', $data);
    }

    //registerd seller page
    public function pendingSeller(Request $request){
        $admin = null;
        if( Auth::guard('admin')->check() ){
            $admin = Admin::findOrFail(auth()->id());
        }
        $sellers = DB::table('sellers as a')->selectRaw('a.id, a.name, a.email, a.phone, a.address, a.picture, c.name_en as district, d.name_en as city, e.title  as service1, f.title  as service2, g.title  as service3, IF(a.status = 1, "Verifyed", IF(a.status = 2,"Pending","Not verified")) as status, DATE_FORMAT(a.created_at, "%Y-%m-%d") as date, a.About_Field , a.Certificate ')
                ->leftJoin('districts as c','c.id','=','a.districts')
                ->leftJoin('cities as d','d.id','=','a.cities')
                ->leftJoin('services as e','e.id','=','a.service1')
                ->leftJoin('services as f','f.id','=','a.service2')
                ->leftJoin('services as g','g.id','=','a.service3')
                ->get();

        $data = [
            'pageTitle'=>'Pending Sellers',
            'sellers'=>$sellers,
            'admin'=>$admin,
        ];

        return view('back.pages.admin.pending-sellers', $data);
    }

    //seller verify
    public function sellerVerify(Request $request){
        //update status
        $seller = Seller::findOrFail($request->seid);
        $seller->status = 1;
        $saved = $seller->save();

        if($saved){ // send mail to seller
            //generate token
            $token = base64_encode(Str::random(64));

            //send email
            $data = array(
                'seller' => $seller,
            );

            $mail_body = view('email-templates.seller-verifed-template',$data)->render();

            $mailConfig = array(
                'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                'mail_from_name' => env('EMAIL_FROM_NAME'),
                'mail_recipient_email' => $seller->email,
                'mail_recipient_name' => $seller->name,
                'mail_subject' => 'Job Post Verified Successful',
                'mail_body' => $mail_body
            );

            sendEmail($mailConfig);

            return response()->json(['status'=>1,'msg'=>'Seller verified successfully completed']);

        }else{
            return response()->json(['status'=>0,'msg'=>'Something went wrong']);
        }

    }

    //seller unverify
    public function sellerUnverify(Request $request){
        $seller = Seller::findOrFail($request->seid);
        $seller->status = 2;
        $saved = $seller->save();

        if($saved){ // send mail to seller
            //generate token
            $token = base64_encode(Str::random(64));

            //send email
            $data = array(
                'seller' => $seller,
            );

            $mail_body = view('email-templates.seller-unverifed-template',$data)->render();

            $mailConfig = array(
                'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                'mail_from_name' => env('EMAIL_FROM_NAME'),
                'mail_recipient_email' => $seller->email,
                'mail_recipient_name' => $seller->name,
                'mail_subject' => 'Job Post Request Unverified',
                'mail_body' => $mail_body
            );

            sendEmail($mailConfig);

            return response()->json(['status'=>1,'msg'=>'Seller job request unverified']);

        }else{
            return response()->json(['status'=>0,'msg'=>'Something went wrong']);
        }



    }
}
