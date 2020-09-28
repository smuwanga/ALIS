<?php

use Illuminate\Support\MessageBag;

/**
 *Contains functions for managing users
 *
 */
class UserController extends Controller {

    //Function for user authentication logic
    public function loginAction(){

        if (Input::server("REQUEST_METHOD") == "POST")
        {
            $validator = Validator::make(Input::all(), array(
                "username" => "required|min:4",
                "password" => "required|min:6"
            ));

            if ($validator->passes())
            {
                $credentials = array(
                    "username" => Input::get("username"),
                    "password" => Input::get("password")
                    );

                if(Auth::attempt($credentials)){
                    return Redirect::route("user.home");
                }

            }
            return Redirect::route('user.login')->withInput(Input::except('password'))
                ->withErrors($validator)
                ->with('message', trans('messages.invalid-login'));
        }

        return View::make("user.login");
    }


    public function configureFacilitySettings()

    {
        if (Input::server("REQUEST_METHOD") == "POST")
        {
            $validator = Validator::make(Input::all(), array(
                "username" => "required|min:4",
                "password" => "required|min:6",
//                "url" => "required"
            ));

            if ($validator->passes())
            {
                $facility_settings = Input::all();

                $file = fopen('config.properties', 'w');
                fwrite($file, json_encode($facility_settings));

                $credentials = array(
                    "username" => Input::get("username"),
                    "password" => Input::get("password"),
//                    "url" => Input::get("url"),
                );

//                if(Auth::attempt($credentials)){
////                    return Redirect::route("user.home");
//                    return Redirect::route("facility.settings");
//                }

            }
            return Redirect::route('facility.settings')->withInput(Input::except('password'))
                ->withErrors($validator)
                ->with('message', trans('messages.invalid-login'));
        }

        return View::make("user.facilitySettings");
    }


    public function logoutAction(){
        Auth::logout();
        return Redirect::route("user.login");
    }

    public function homeAction(){
        return View::make("user.home");
    }

	public function dashboard(){
        return View::make("user.dashboard");
    }


    /**
     * Display a listing of the users.
     *
     * @return Response
     */
    public function index()
    {
        // List all the active users
            $users = User::orderBy('name', 'ASC')->get();

        // Load the view and pass the users
        return View::make('user.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //Create User
        return View::make('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
        $rules = array(
            'username' => 'alpha_num|required|unique:users,username|min:4',
            'password' => 'confirmed|required|min:6',
            'full_name' => 'required',
            //'email' => 'required|email'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::route('user.create')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $user = new User;
            $user->username = Input::get('username');
            $user->name = Input::get('full_name');
            $user->gender = Input::get('gender');
            $user->phone_contact = Input::get('phone_contact');
            $user->designation = Input::get('designation');
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            // todo this facility id has to be removed from here since the system is local
            // leaving it here because of dependencies
            $user->facility_id = 1;

            $user->save();
            $id = $user->id;

            if (Input::hasFile('image')) {
                try {
                    $extension = Input::file('image')->getClientOriginalExtension();
                    $destination = public_path().'/i/users/';
                    $filename = "user-$id.$extension";

                    $file = Input::file('image')->move($destination, $filename);
                    $user->image = "/i/users/$filename";

                } catch (Exception $e) {}
            }

            try{
                $user->save();
                return Redirect::route('user.index')->with('message', trans('messages.success-creating-user'));
            }catch(QueryException $e){
                Log::error($e);
                return Redirect::route('user.index')
                    ->with('message', trans('messages.failure-creating-user'));
            }

            // redirect
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //Show a user
        $user = User::find($id);

        //Show the view and pass the $user to it
        return View::make('user.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //Get the user
        $user = User::find($id);

        //Open the Edit View and pass to it the $user
        return View::make('user.edit')->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
        $rules = array(
            'username' => 'alpha_num|required:users,username|min:4',
            'full_name' => 'required',
            'image' => 'image|max:500'
        );

        if (Input::get('reset-password')) {
            $rules['reset-password'] = 'min:6';
        }

        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::route('user.edit', array($id))
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // Update
            $user = User::find($id);
            $user->username = Input::get('username');
            $user->name = Input::get('full_name');
            $user->gender = Input::get('gender');
            $user->phone_contact = Input::get('phone_contact');
            $user->designation = Input::get('designation');
            $user->email = Input::get('email');

            if (Input::hasFile('image')) {
                try {
                    $extension = Input::file('image')->getClientOriginalExtension();
                    $destination = public_path().'/i/users/';
                    $filename = "user-$id.$extension";

                    $file = Input::file('image')->move($destination, $filename);
                    $user->image = "/i/users/$filename";

                } catch (Exception $e) {
                    Log::error($e);
                }
            }

            //Resetting passwords - by the administrator
            if (Input::get('reset-password')) {
                $user->password = Hash::make(Input::get('reset-password'));
            }

            $user->save();

            // redirect
            $url = Session::get('SOURCE_URL');

            return Redirect::to($url)->with('message', trans('messages.user-profile-edit-success')) ->with('activeuser', $user ->id);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function updateOwnPassword($id)
    {
        //
        $rules = array(
            'current_password' => 'required|min:6',
            'new_password'  => 'confirmed|required|min:6',
        );

        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::route('user.edit', array($id))->withErrors($validator);
        } else {
            // Update
            $user = User::find($id);
            // change password if parameters were entered (changing ones own password)
            if (Hash::check(Input::get('current_password'), $user->password))
            {
                $user->password = Hash::make(Input::get('new_password'));
            }else{
                return Redirect::route('user.edit', array($id))
                        ->withErrors(trans('messages.incorrect-current-passord'));
            }

            $user->save();
        }

        // redirect
        $url = Session::get('SOURCE_URL');

        return Redirect::to($url)->with('message', trans('messages.user-profile-edit-success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage (soft delete).
     *
     * @param  int  $id
     * @return Response
     */
    public function delete($id)
    {
        //Soft delete the user
        $user = User::find($id);

        $user->delete();

        // redirect
        $url = Session::get('SOURCE_URL');

        return Redirect::to($url)->with('message', trans('messages.success-deleting-user'));
    }
}
