<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
  public function index()
  {
      return view('users.index');
  }


  
  //Control admin account 
  public function view_admin_update(){
      $users = User::orderByRaw("utype = 'ADM' DESC")
              -> latest()
              -> paginate(4);
      return view('admin.users.index', compact('users'));
  }


  public function update_role(Request $request, User $user){
      $request -> validate([
        'utype' => 'required|in:ADM,USER',
      ]);

      // Preven removing your own admin access
      if($user->id === Auth::id() && $request->utype !== 'ADM'){
        return back()->with('error', 'You cannot remove your own admin pivileges.');
      }

      // Optional : Limit maximum number of admins to 3
      if($request->utype === 'ADM' && $user->utype !== 'ADM'){
        if(User::where('utype', 'ADM')->count() >= 3){
          return back()->with('error', 'Maximum of 3 admins allowed.');
        }
      }

    
      $user->update(['utype' => $request->utype]);
      $user->save();
      
      return back()->with('success', "updated {$user->name}'s role successfully.");
    
  }
}
