<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;

class WorkshopController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show()
    {
        return view('show-workshop');
    }

    public function get()
    {
        return response()->json(['workshops' => Timetable::all()->toArray()]);
    }

    public function delete($id)
    {
        $status = Timetable::find($id)->delete();
        return response()->json(['succsess' => $status]);
    }

    public function update($id, Request $request)
    {
        $timetable = Timetable::findOrFail($id);

        $this->validate($request, [
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_guests' => 'required'
        ]);

        $input = $request->all();
        $status = $timetable->fill($input)->save();
        
        return response()->json(['succsess' => $status]);
    }

    public function store(Request $request)
    {
        $timetable = new Timetable;

        $timetable->day = $request->day;
        $timetable->start_time = $request->startTime;
        $timetable->end_time = $request->endTime;
        $timetable->max_guests = $request->maxGuests;

        $status = $timetable->save();
        return response()->json(['succsess' => $status]);
    }
}
