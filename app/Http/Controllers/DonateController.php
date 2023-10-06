<?php

namespace App\Http\Controllers;

use App\Models\Donate;
use App\Models\Donor;
use Illuminate\Http\Request;

class DonateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donates = Donate::paginate();
        return view('admin.donates.index', compact('donates'));
    }
    public function donorDonates(Donor $donor)
    {
        $donates = Donate::where('donor_id', $donor->id)->paginate();
        return view('admin.donates.indexDonorDonates', compact('donates', 'donor'));
    }
    /**
     * Create a new donor.
     *
     * @param Donor $donor The donor object.
     * @return View The view for creating a new donation with a list of programs.
     */
    public function create(Donor $donor)
    {
        $programs = \App\Models\Program::all();
        return view('admin.donates.create', compact('programs'));
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
            'value' => 'required|numeric|min:5|max:5000',
            'donor_id' => 'required|exists:donors,id',
            'program_id' => 'required|exists:programs,id',
        ]);
        $donate = Donate::create([
            'value' => $request->value,
            'donor_id' => $request->donor_id,
            'program_id' => $request->program_id,
        ]);

        $user = Donor::find($request->donor_id);

        $dataPay = [
            'amount' =>  $request->value,
            'user_id' => $user->id,
            'user_first_name' => $user->user->name,
            'user_email' => $user->user->email,
            'user_phone' => $user->user->phone,
            'donate_id' => $donate->id,
        ];

        return redirect()->route('admin.payment-paytabs', $dataPay);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Donate  $donate
     * @return \Illuminate\Http\Response
     */
    public function show(Donate $donate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Donate  $donate
     * @return \Illuminate\Http\Response
     */
    public function edit(Donate $donate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Donate  $donate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Donate $donate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Donate  $donate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Donate $donate)
    {
        //
    }
}
