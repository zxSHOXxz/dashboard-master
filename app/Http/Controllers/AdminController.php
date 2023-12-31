<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{



    public function __construct()
    {
        $this->middleware('can:admins-create', ['only' => ['create', 'store']]);
        $this->middleware('can:admins-read',   ['only' => ['show', 'index']]);
        $this->middleware('can:admins-update',   ['only' => ['edit', 'update']]);
        $this->middleware('can:admins-delete',   ['only' => ['delete']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admins = Admin::whereHas('user', function ($q) use ($request) {
            if ($request->id != null) {
                $q->where('id', $request->id);
            }
            if ($request->q != null) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('phone', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->q . '%');
                });
            }
        })
            ->with(['roles', 'user' => function ($query) {
                $query->withCount(['logs', 'articles', 'contacts']);
            }])
            ->orderBy('id', 'DESC')
            ->paginate();

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get();
        return view('admin.admins.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => "nullable|max:190",
            'phone' => "nullable|max:190",
            'bio' => "nullable|max:5000",
            'blocked' => "required|in:0,1",
            'email' => "required|unique:admins,email",
            'password' => "required|min:8|max:190"
        ]);
        $admin = new Admin();
        $admin->email = $request->email;
        $admin->password =  \Hash::make($request->password);
        $admin->save();
        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        $user->blocked = $request->blocked;
        $user->email_verified_at = date("Y-m-d h:i:s");
        $user->actor()->associate($admin);
        $user->save();
        if ($request->hasFile('avatar')) {
            $avatar = $user->addMedia($request->avatar)->toMediaCollection('avatar');
            $user->update(['avatar' => $avatar->id . '/' . $avatar->file_name]);
        }
        $user->save();
        if (auth()->user()->can('user-roles-update')) {
            $request->validate([
                'roles' => "required|array",
                'roles.*' => "required|exists:roles,id",
            ]);
            $admin->syncRoles($request->roles);
        }
        toastr()->success('تم إضافة المستخدم بنجاح', 'عملية ناجحة');
        return redirect()->route('admin.admins.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        $roles = Role::get();
        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => "nullable|max:190",
            'phone' => "nullable|max:190",
            'bio' => "nullable|max:5000",
            'blocked' => "required|in:0,1",
            'email' => "required|unique:admins,email," . $admin->id,
            'password' => "nullable|min:8|max:190"
        ]);
        $admin->email = $request->email;
        $admin->password =  \Hash::make($request->password);
        $admin->save();
        $user = User::findOrFail($admin->user->id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        $user->blocked = $request->blocked;
        $user->email_verified_at = date("Y-m-d h:i:s");
        $user->actor()->associate($admin);
        $user->save();
        if ($request->hasFile('avatar')) {
            $avatar = $user->addMedia($request->avatar)->toMediaCollection('avatar');
            $user->update(['avatar' => $avatar->id . '/' . $avatar->file_name]);
        }
        $user->save();
        if (auth()->user()->can('user-roles-update')) {
            $request->validate([
                'roles' => "required|array",
                'roles.*' => "required|exists:roles,id",
            ]);
            $admin->syncRoles($request->roles);
        }
        toastr()->success('تم تحديث المستخدم بنجاح', 'عملية ناجحة');
        return redirect()->route('admin.admins.show', $admin);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(\App\Models\Admin $admin)
    {
        if (!auth()->user()->can('admins-delete')) abort(403);
        $admin->user()->delete();
        $admin->delete();
        toastr()->success('تم حذف المستخدم بنجاح', 'عملية ناجحة');
        return redirect()->route('admin.admins.index');
    }

    public function access(Request $request, Admin $admin)
    {
        if (auth()->user()->hasRole('superadmin')) {
            auth()->logout();
            auth()->loginUsingId($admin->id);
            return redirect('/admin');
        }
    }
}
