<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AllUserResource;
use App\Notifications\WelcomeTeamNotification;

class UserController extends Controller
{
    /**
     * Get authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        return new UserResource($request->user());
    }

    public function allUser(){
        $users = User::where('is_active', true)->get();
    
        return AllUserResource::collection($users);
    }

    /**
     * Get all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return UserResource::collection(User::latest()->paginate($request->perPage));
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->term;
        $query = User::query();

        $query->where(function ($query) use ($term) {
            $query->where('name', 'Like', '%' . $term . '%')
                ->orWhere('email', 'Like', '%' . $term . '%');
        });

        return UserResource::collection($query->latest()->paginate($request->perPage));
    }

    /**
     * Show user by slug
     */
    public function show($slug)
    {
        return User::where('slug', $slug)->with('roles')->first();
    }

    /**
     * Store data
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email:filter|max:255|unique:users',
            'role' => 'required',
            'password' => 'nullable|min:6|required_with:password_confirmation|same:password_confirmation',
        ]);

        $password = $request->password ? $request->password : 12345678;
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'account_role' => 1,
            'is_active' => 1,
        ]);

        $user->email_verified_at = now();
        $user->save();

        $roleId = Role::where('slug', $request->role)->pluck('id')->toArray();
        $user->roles()->attach($roleId);

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($user)
        ->withProperties([
            'name' => $request->name,
            'code' =>  '[' . $request->name . ']',
            'event' => 'Create',
            'slug' => '/',
            'routeName' => 'user.index'
        ])
        ->useLog('User Created')
        ->log('User Created');

        if ($request->mail) {
            $user->notify(new WelcomeTeamNotification($password));
        }

        return $this->responseWithSuccess('Team member added successfully.', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $team = User::where('slug', $slug)->with('roles')->first();

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $team->id,
            'password' => 'nullable|min:6|required_with:password_confirmation|same:password_confirmation',
            'role' => 'required',
        ]);

        $team->name = $request->name;
        $team->email = $request->email;
        if ($request->password) {
            $team->password = Hash::make($request->password);
        }
        $team->save();

        // update role
        if ($request->role && count($team->roles) <= 0) {
            $roleId = Role::where('slug', $request->role)->pluck('id')->toArray();
            $team->roles()->attach($roleId);
        } else {
            if ($request->role != $team->roles[0]->slug) {
                $team->roles()->detach();
                $roleId = Role::where('slug', $request->role)->pluck('id')->toArray();
                $team->roles()->attach($roleId);
            }
        }

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($team)
        ->withProperties([
            'name' => $request->name,
            'code' =>  '[' . $request->name . ']',
            'event' => 'Update',
            'slug' => '/',
            'routeName' => 'user.index'
        ])
        ->useLog('User Updated')
        ->log('User Updated');

        return $this->responseWithSuccess('User updated successfully.', $team);
    }

    /**
     * search resource from storage.
     */
    public function roles()
    {
        return Role::select('name', 'slug')->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $user = User::where('slug', $slug)->first();

        // add activity log
        activity()
        ->causedBy(Auth::user())
        ->performedOn($user)
        ->withProperties([
            'name' => $user->name,
            'code' =>  '[' . $user->name . ']',
            'event' => 'Delete',
            'slug' => '/',
            'routeName' => ''
        ])
        ->useLog('User Deleted')
        ->log('User Deleted');

        if ($user) {
            $user->delete();

            return $this->responseWithSuccess('User deleted successfully', $user);
        }

        return $this->responseWithError('Opps! You are trying with bad request..');
    }
}