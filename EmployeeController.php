<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CSVFileController;


//use DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //$stores = addCommaToArray(ObjectToSingleArray(getSitesByEmployee(),'Location'));

        //DB::enableQueryLog();

        $employees = DB::table('employees')
            ->select('*')
            ->join('users', 'employees.EmployeeID', '=', 'users.EmployeeID')
            ->join('job_details', 'employees.EmployeeID', '=', 'job_details.EmployeeID')
            ->where('employees.Approved', 1)
            ->where('employees.CompanyID', session('COMPANY_ID'))
            ->where('employees.Leave', 0)
            ->where('users.FormFilled', 1)
            // ->where(function ($query) use ($stores){
            //     $query->where('job_details.Location', session('branch'))
            //     ->orWhereIn('job_details.Location', $stores);
            // })
            // ->where(function ($query) use ($stores){
            //     $query->whereIn('job_details.Location', $stores);
            // })
            //->whereIn('job_details.Location', $stores)
            ->where('users.Inactiv', 0)
            ->orderByDesc('employees.EmployeeID')
            ->get();

        //dd($employees);

        //dd( DB::getQueryLog() );

        //dd($employees->toSql());

        return view('employee.index', compact('employees'));
    }

    public function archives()
    {
        $archives = DB::table('employees')
            ->select('employees.*', 'job_details.*', 'users.is_logged', 'leaver.Date as LeaverDate')
            ->Join('users', 'employees.EmployeeID', '=', 'users.EmployeeID')
            ->join('job_details', 'employees.EmployeeID', '=', 'job_details.EmployeeID')
            ->join('leaver', 'employees.EmployeeID', '=', 'leaver.EmployeeID')
            ->where('employees.Approved', 1)
            ->where('employees.CompanyID', session('COMPANY_ID'))
            ->where('employees.Leave', 1)
            ->where('users.FormFilled', 1)
            ->where('users.Inactiv', 1)
            ->orderByDesc('employees.EmployeeID')
            ->get();

        return view('employee.archives', compact('archives'));
    }

    public function leavers()
    {
        $leavers = DB::table('leaver')
            ->select('employees.*', 'job_details.*', 'users.is_logged', 'leaver.*')
            ->Join('users', 'leaver.EmployeeID', '=', 'users.EmployeeID')
            ->Join('job_details', 'leaver.EmployeeID', '=', 'job_details.EmployeeID')
            ->Join('employees', 'leaver.EmployeeID', '=', 'employees.EmployeeID')
            ->where('employees.Approved', 1)
            ->where('employees.CompanyID', session('COMPANY_ID'))
            ->where('employees.Leave', 0)
            ->where('users.FormFilled', 1)
            ->where('users.Inactiv', 0)
            ->orderByDesc('employees.EmployeeID')
            ->get();

        return view('employee.leavers', compact('leavers'));
    }

    public function externals()
    {
        $externals = DB::table('users')
            ->select('*')
            ->where('CompanyID', session('COMPANY_ID'))
            ->where('FormFilled', 1)
            ->where('Inactiv', 0)
            ->where('Type', '!=', '')
            // ->where('Type', 'external')
            // ->orwhere('Type', 'mixed')
            ->orderByDesc('UserID')
            ->get();

        return view('employee.externals', compact('externals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     Employee::insert([
    //         'CompanyID' => '1',
    //         'title' => $request->title,
    //         'address' => $request->address,
    //         'email' => $request->email,
    //         'qrcode' => $request->qrcode,
    //         'lat' => $request->lat,
    //         'long' => $request->long,
    //         'status' => $request->status,
    //         'created_at' => date('Y-m-d H:i:s')
    //     ]);
    //     return redirect()->route('employee.index')->with('success', 'Employee Added');
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    // public function show(Employee $employee)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    // public function edit(Employee $employee)
    // {

    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Employee $employee)
    // {
    //     $employee->title = $request->title;
    //     $employee->address = $request->address;
    //     $employee->email = $request->email;
    //     $employee->qrcode = $request->qrcode;
    //     $employee->lat = $request->lat;
    //     $employee->long = $request->long;
    //     $employee->status = $request->status;
    //     $employee->updated_at = date('Y-m-d H:i:s');
    //     $employee->update();
    //     return redirect()->route('employee.index')->with('success', 'Employee Updated');
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Employee $employee)
    // {
    //     //
    // }

    public function leaver(Request $r, $id)
    {
        //dd($id);

        $d = getPersonalDetails($id);

        //$name = $_SESSION['fname']." ".$_SESSION['lname'];

        $name = 'TEst';

        if (empty($r->membership)) {
            $membership = "No applicable";
        } else {
            $membership = $r->membership;
        }

        $leavers = DB::table('leaver')->insert([
            'Date' => DMYFormat($r->leave_date),
            'Type' => $r->type,
            'FirstName' => $d->FirstName,
            'MiddleName' => $d->MiddleName,
            'LastName' => $d->LastName,
            'Reason' => convertBrToString($r->reason),
            'Warnings' => $r->warnings,
            'WarningDetails' => convertBrToString($r->w_details),
            'HRconsulted' => $r->hrc,
            'Interview' => $r->interview,
            'Minutes' => $r->minutes,
            'ManagerName' => $name,
            'Membership' => $membership,
            'EmployeeID' => $id
        ]);

        // $HR = getHRbyCompany(1);
        // StaffNotify($HR->EmailAddress,$HR->FirstName,$manager,$_SESSION['branch'],ucfirst($d->FirstName)." ".ucfirst($d->LastName),$HR->Rank);

        return redirect()->route('employee.index')->with('success', 'Leaver Updated');
    }

    public function leaverForm(Request $r, $id)
    {
        if ($r->Approve == 'Approve') {
            UpdateELeaver($id);
            UpdateULeaver($id);

            // EmployeeNotifyDismissal($employee_email,$ex->FirstName." ".$ex->LastName,$ex->Reason,$ex->Date);

            // $staff_email = $members->getUserDetailsByCompany($rank,$_SESSION["CompanyID"]);
            // foreach($staff_email as $email) {
            //     StaffNotify($email->EmailAddress,$email->FirstName,$ex->ManagerName,$job->Location,$ex->FirstName." ".$ex->LastName,$email->Rank);
            // }

            // //get Accounts details for sending email
            // $accounts = $members->getAccounts();
            // foreach($accounts as $ac){
            //     AccountsLeaverNotify($ac->EmailAddress,$ac->FirstName,$ex->FirstName." ".$ex->LastName);
            // }

        } else if ($r->Reject == 'Reject') {
            // DB::table('leaver')->where('LeaveID', $id)->update([
            //     'status' => '0'
            // ]);

            DB::table('leaver')->where('LeaveID', $id)->delete();

            //$delete = $members->deleteLeaver($_GET['id']);
            // $m = $members->getManagerRegister($job->Location);
            // RejectedrNotify($m->EmailAddress,$m->FirstName,$_POST["reason"],$ex->FirstName." ".$ex->LastName);
        }

        return redirect()->route('employee.leavers')->with('success', 'Leaver Updated');
    }

    public function show($id)
    {
        //
    }

    public function site($empid, $userid)
    {
        $sites = getSitesByCompany();
        $emp_sites = DB::table('stores')
            ->select('stores.Location', 'stores.StoreID', 'emp_sites.status')
            ->Join('emp_sites', 'stores.StoreID', '=', 'emp_sites.StoreID')
            ->where('emp_sites.EmployeeID', $empid)
            ->where('emp_sites.UserID', $userid)
            ->get();

        $location = DB::table('job_details')
            ->select('job_details.Location')
            ->where('EmployeeID', $empid)
            ->value('job_details.Location');
        return view('employee.site', compact('sites', 'emp_sites', 'userid', 'empid', 'location'));
    }

    public function section($empid, $userid)
    {
        $section = getSectionsByCompany();
        $emp_sec = DB::table('emp_section')
            ->where("EmployeeID", "=", $empid)
            ->where("UserID", "=", $userid)
            ->get();
        return view('employee.section', compact('section', 'userid', 'empid', 'emp_sec'));
    }


    public function userSites()
    {
        $users =  getUsers();
        $sites = getSites();
        return view('employee.sites', compact('users', 'sites'));
    }




    public function getCSVColums($id)
    {

        $colums = DB::getSchemaBuilder()->getColumnListing('employees');
        return view('employee.CSVFile', ['colums' => $colums]);
    }




    public function exportSingleCSV(Request $request, $id)
    {
        $obj =  getPersonalDetails($id);
        $name = $obj->FirstName.$obj->LastName;
        $colums = array();
        $ss = $request->all();

        foreach ($ss as $arr) {
            $colums[] = $arr;
        }
        array_shift($colums);
        return (new CSVFileController)->getCsv($id, $colums,$name);
    }
}
