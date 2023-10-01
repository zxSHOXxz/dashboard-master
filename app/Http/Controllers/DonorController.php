<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DonorController extends Controller
{


    public function __construct()
    {
        $this->middleware('can:donors-create', ['only' => ['create', 'store']]);
        $this->middleware('can:donors-read',   ['only' => ['show', 'index']]);
        $this->middleware('can:donors-update',   ['only' => ['edit', 'update']]);
        $this->middleware('can:donors-delete',   ['only' => ['delete']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $donors = Donor::whereHas('user', function ($q) use ($request) {
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
                $query->withCount(['logs']);
            }])->orderBy('id', 'DESC')
            ->paginate();

        return view('admin.donors.index', compact('donors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'donor')->get();
        return view('admin.donors.create', compact('roles'));
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
            'email' => "required|unique:donors,email",
            'password' => "required|min:8|max:190"
        ]);
        $donor = new Donor();
        $donor->email = $request->email;
        $donor->password =  \Hash::make($request->password);
        $donor->save();
        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        $user->blocked = $request->blocked;
        $user->email_verified_at = date("Y-m-d h:i:s");
        $user->actor()->associate($donor);
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
            $donor->syncRoles($request->roles);
        }
        toastr()->success('تم إضافة المستخدم بنجاح', 'عملية ناجحة');
        return redirect()->route('admin.donors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function show(Donor $donor)
    {
        return view('admin.donors.show', compact('donor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function edit(Donor $donor)
    {
        $roles = Role::where('guard_name', 'donor')->get();
        return view('admin.donors.edit', compact('donor', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Donor $donor)
    {
        $request->validate([
            'name' => "nullable|max:190",
            'phone' => "nullable|max:190",
            'bio' => "nullable|max:5000",
            'blocked' => "required|in:0,1",
            'email' => "required|unique:donors,email," . $donor->id,
            'password' => "required|min:8|max:190"
        ]);
        $donor->email = $request->email;
        $donor->password =  \Hash::make($request->password);
        $donor->save();
        $user = User::findOrFail($donor->user->id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        $user->blocked = $request->blocked;
        $user->email_verified_at = date("Y-m-d h:i:s");
        $user->actor()->associate($donor);
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
            $donor->syncRoles($request->roles);
        }
        toastr()->success('تم إضافة المستخدم بنجاح', 'عملية ناجحة');
        return redirect()->route('admin.donors.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Donor  $donor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Donor $donor)
    {
        if (!auth()->user()->can('donors-delete')) abort(403);
        $donor->user()->delete();
        $donor->delete();
        toastr()->success('تم حذف المستخدم بنجاح', 'عملية ناجحة');
        return redirect()->route('admin.donors.index');
    }

    /**
     * Access the system with a given request and donor.
     *
     * @param Request $request The request object.
     * @param Donor   $donor   The donor object.
     *
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */

    public function access(Request $request, Donor $donor)
    {
        if (auth()->user()->hasRole('superadmin')) {
            auth()->logout();
            auth()->loginUsingId($donor->id);
            return redirect('/admin');
        }
    }
}
