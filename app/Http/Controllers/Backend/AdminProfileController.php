<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function AdminProfile()
    {
        $adminData = Admin::find(1);

        return view('admin.admin_profile_view', compact('adminData'));
    } // End Method

    public function AdminProfileEdit()
    {
        $editData = Admin::find(1);

        return view('admin.admin_profile_edit', compact('editData'));
    }// End Method

    public function AdminProfileStore(Request $request)
    {
        $data = Admin::find(1);

        $data->name = $request->name;
        $data->email = $request->email;

        if($request->file('profile_photo_path'))
        {
            $file = $request->file('profile_photo_path');

            if($data->profile_photo_path){
                // Supprime l'image précédente si elle existe
                $previousImagePath = public_path('upload/admin_images/'.$data->profile_photo_path);
                if(file_exists($previousImagePath)){
                    unlink($previousImagePath);
                }
            }
            
            //@unlink(public_path('upload/admin_images/'.$data->profile_photo_path)); // Supprime l'image précédente si elle existe

            $filename = date('YmdHi'). $file->getClientOriginalName();

            $file->move(public_path('upload/admin_images'), $filename);
            $data['profile_photo_path'] = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('admin.profile')->with($notification);
    }// End Method

    public function AdminChangePassword()
    {
        return view('admin.admin_change_password');
    }// End Method

    public function AdminUpdateChangePassword(Request $request)
    {
        $validateData = $request->validate([
            'oldpassword' => 'required',
            'password' => 'required|confirmed',
        ]);

        $hashedPassword = Admin::find(1)->password;

        if(Hash::check($validateData['oldpassword'], $hashedPassword))
        {
            $admin = Admin::find(1);
            $admin->password = Hash::make($request->password);
            $admin->save();

            Auth::logout();
            return redirect()->route('admin.logout');

        }else{
            return redirect()->back();
        }
        
    }// End Method
}
