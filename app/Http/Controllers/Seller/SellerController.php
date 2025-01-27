<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\DB;
use constGuards;
use constDefaults;
use Illuminate\Support\Facades\File;
// use Mberecall\Kropify\Kropify;
// use App\Models\Shop;

class SellerController extends Controller
{
    //login
    public function login(Request $request){
        $data = [
            'pageTitle'=>'Seller Login'
        ];
        return view('back.pages.seller.auth.login',$data);
    }

    //register
    public function register(Request $request){
        $data = [
            'pageTitle'=>'Create Seller Account'
        ];
        return view('back.pages.seller.auth.register',$data);
    }

    //seller home
    public function home(Request $request){
        $data = [
            'pageTitle'=>'Seller Dashboard'
        ];
        return view('back.pages.seller.home',$data);
    }

    public function registerSuccess(Request $request){
        return view('back.pages.seller.register-success');
    }

    //create seller account
    public function createSeller(Request $request){
        //Validate Seller Registration Form
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:sellers',
            'password'=>'min:5|required_with:confirm_password|same:confirm_password',
            'confirm_password'=>'min:5'
        ]);

        $seller = new Seller();
        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->password = Hash::make($request->password);
        $seller->status = "0";
        $saved = $seller->save();

        if( $saved ){
        //    //Generate token
        //    $token = base64_encode(Str::random(64));

        //    VerificationToken::create([
        //       'user_type'=>'seller',
        //       'email'=>$request->email,
        //       'token'=>$token
        //    ]);

        //    $actionLink = route('seller.verify',['token'=>$token]);
        //    $data['action_link'] = $actionLink;
        //    $data['seller_name'] = $request->name;
        //    $data['seller_email'] = $request->email;

        //    //Send Activation link to this seller email
        //    $mail_body = view('email-templates.seller-verify-template',$data)->render();

        //    $mailConfig = array(
        //       'mail_from_email'=>env('EMAIL_FROM_ADDRESS'),
        //       'mail_from_name'=>env('EMAIL_FROM_NAME'),
        //       'mail_recipient_email'=>$request->email,
        //       'mail_recipient_name'=>$request->name,
        //       'mail_subject'=>'Verify Seller Account',
        //       'mail_body'=>$mail_body
        //    );

        //    if( sendEmail($mailConfig) ){
        //       return redirect()->route('seller.register-success');
        //    }else{
        //      return redirect()->route('seller.register')->with('fail','Something went wrong while sending verification link.');
        //    }
            // return redirect()->route('seller.register-success');
            return redirect()->route('seller.login')->with('success','Good!, Registation successful. Login with your credentials and complete setup your Technicians account.');
        }else{
            return redirect()->route('seller.register')->with('fail','Something went wrong.');
        }
    }

    //login function
    public function loginHandler(Request $request){
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if( $fieldType == 'email' ){
            $request->validate([
                'login_id'=>'required|email|exists:sellers,email',
                'password'=>'required|min:5|max:45'
            ],[
                'login_id.required'=>'Email or Username is required.',
                'login_id.email'=>'Invalid email address.',
                'login_id.exists'=>'Email is not exists in system.',
                'password.required'=>'Password is required'
            ]);
        }else{
            $request->validate([
                'login_id'=>'required|exists:sellers,username',
                'password'=>'required|min:5|max:45'
            ],[
                'login_id.required'=>'Email or Username is required.',
                'login_id.exists'=>'Username is not exists in system.',
                'password.required'=>'Password is required'
            ]);
        }

        $creds = array(
            $fieldType => $request->login_id,
            'password' => $request->password
        );

        if( Auth::guard('seller')->attempt($creds) ){

            if( !auth('seller')->user()->verified ){
                return redirect()->route('seller.seller-details');
            }else{
                return redirect()->route('seller.home');
            }
        }else{
            return redirect()->route('seller.login')->withInput()->with('fail','Incorrect password.');
        }
    }

    //logout
    public function logoutHandler(Request $request){
        Auth::guard('seller')->logout();
        return redirect()->route('seller.login')->with('fail','You are logged out!');
    }

    //seller details
    public function sellerDetails(Request $request){
        $seller = null;
        if( Auth::guard('seller')->check() ){
            $seller = Auth::guard('seller')->user();
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();
        $services = DB::table('services')->get();

        $data = [
            'pageTitle'=>'Seller Details',
            'seller'=>$seller,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities,
            'services'=>$services
        ];
        return view('back.pages.seller.seller-details',$data);
    }

    //change profile picture
    public function changeProfilePicture(Request $request){


        $seller = Seller::findOrFail(auth()->id());
        $path = 'images/users/sellers/';

        if($request->hasFile('sellerProfilePictureFile')){
            $file = $request->file('sellerProfilePictureFile');
            $old_picture = $seller->getAttributes()['picture'];
            $file_path = $path.$old_picture;
            $filename = 'SELLER_IMG_'.rand(2,1000).$seller->id.time().uniqid().'.jpg';

            $upload = $file->move(public_path($path),$filename);
            if($upload){
                if( $old_picture != null && File::exists(public_path($path.$old_picture)) ){
                    File::delete(public_path($path.$old_picture));
                }
                $seller->update(['picture'=>$filename]);
                return response()->json(['status'=>1,'msg'=>'Your profile picture has been successfully updated.']);
            }else{
                return response()->json(['status'=>0,'msg'=>'Something went wrong.']);
            }
        }

        // return redirect()->route('seller.seller-details')->with('success','Profile picture updated successfully');
    }

    //save seller details
    public function saveSellerDetails(Request $request){
        $request->validate([
            'name'=>'required',
            'phone'=>'required',
            'address'=>'required',
            'provinces'=>'required',
            'districts'=>'required',
            'cities'=>'required',
            'service1'=>'required',
            'service2'=>'required',
            'service3'=>'required',
        ]);

        $seller = Seller::findOrFail(auth()->id());
        $seller->name = $request->name;
        $seller->phone = $request->phone;
        $seller->address = $request->address;
        $seller->provinces = $request->provinces;
        $seller->districts = $request->districts;
        $seller->cities = $request->cities;
        $seller->service1 = $request->service1;
        $seller->service2 = $request->service2;
        $seller->service3 = $request->service3;
        $seller->verified = "1";

        $saved = $seller->save();
        if( $saved ){
            return redirect()->route('seller.home')->with('success','Seller details updated successfully.');
        }else{
            return redirect()->route('seller.seller-details')->with('fail','Something went wrong.');
        }

    }

    //profile view
    public function profileView(Request $request){
        $seller = null;
        if( Auth::guard('seller')->check() ){
            $seller = Seller::findOrFail(auth()->id());
        }
        $provinces = DB::table('provinces')->get();
        $districts = DB::table('districts')->get();
        $cities = DB::table('cities')->get();
        $services = DB::table('services')->get();
        $data = [
            'pageTitle'=>'Seller Profile',
            'seller'=>$seller,
            'provinces'=>$provinces,
            'districts'=>$districts,
            'cities'=>$cities,
            'services' =>$services,
        ];

        return view('back.pages.seller.profile', $data);
    }

    //update profile
    public function updateProfile(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'username' => 'required',
            'address' => 'required',
            'phone'=> 'required',
            'provinces' => 'required',
            'districts' => 'required',
            'cities' => 'required',
            'service1' => 'required',
            'service2' => 'required',
            'service3' => 'required',
        ]);

        $seller = Seller::findOrFail(auth()->id());
        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->username = $request->username;
        $seller->address = $request->address;
        $seller->phone = $request->phone;
        $seller->provinces = $request->provinces;
        $seller->districts = $request->districts;
        $seller->cities = $request->cities;
        $seller->service1 = $request->service1;
        $seller->service2 = $request->service2;
        $seller->service3 = $request->service3;
        $saved = $seller->save();

        if($saved){
            return redirect()->route('seller.profile')->with('success','Profile updated successfully');
        }else{
            return redirect()->route('seller.profile')->with('fail','Something went wrong.');
        }

        // $this->showToastr('success','Your personal details have been successfully updated.');

        // return redirect()->route('admin.profile')->with('success','Profile updated successfully');
    }

    //job
    public function job(Request $request){
        $seller = null;
        if( Auth::guard('seller')->check() ){
            $seller = Seller::findOrFail(auth()->id());
        }
        $data = [
            'pageTitle'=>'Post Job',
            'seller'=>$seller,
        ];
        return view('back.pages.seller.job',$data);
    }

    //job verify
    public function sellerDocumentUpload(Request $request){
        $request->validate([
            'comment'=>'required',
            'Certificate' => 'image|mimes:jpg,jpeg,png'
        ]);

        $seller = Seller::findOrFail(auth()->id());
        $seller->About_Field  = $request->comment;
        $seller->status  = "2";

        if($request->hasFile('Certificate')){
            $file = $request->file('Certificate');
            $extension = $file->getClientoriginalExtension();
            $path = 'images/users/sellers/certificate';
            $filename = 'SELLER_CERTIFICATE_'.rand(2,1000).$seller->id.time().uniqid().'.'.$extension;

            $upload = $file->move($path,$filename);
            if($upload){
                $seller->Certificate = $filename;
                $seller->save();

                return redirect()->route('seller.job')->with('success','Your Certificate has been successfully uploaded.');
            }else{
                return redirect()->route('seller.profile')->with('fail','Something went wrong.');
            }
        }

    }
}
