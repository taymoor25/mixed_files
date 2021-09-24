   DB::enableQueryLog();
   dd(DB::getQueryLog());
   <!-- query -->
   DB::table('su_new_arrivals')->insert([
   'pick_up_location' =>,
   ])
   ->orderByRaw('STR_TO_DATE( job_details.StartDate, "%d-%m-%Y") DESC')


   <!-- functions  -->
   request()->route('id')
   return redirect()->back()->with(['success'=>'Import Done Successfully']);

   'applicant_ref' => $PortRef,
   'full_name' => $name,
   'contact_number' => $contact_number,
   'created_at' => $checkINDate,
   'dob' => $dob,
   'gender' => $gender,
   'nationality' => $nationality,
   'language' => $language,
   'room_allocated_id' => $room_no,
   'location' => $location,
   'family_code' => $family_number

   'port_ref' => $PortRef,
   'full_name' => $name,
   'dob' => $dob,
   'gender' => $gender,
   'contact_number' => $contact_number,
   'su_id' => $suid,
   'room_allocated_id' => $room_no

   <!-- sessions -->

   session([
   'NickName' => $super_admin->NickName,
   'user_img' => $super_admin->Image,
   'userID' => $super_admin->AdminID,
   'role' => $super_admin->Rank,
   'fname' => $super_admin->FirstName,
   'lname' => $super_admin->LastName,
   'email' => $super_admin->EmailAddress,
   'department' => $super_admin->Departament,
   'istraining' => $super_admin->isTraining,
   'authorized' => $super_admin->Authorized,
   'privilege' => $super_admin->Privilege,
   'logged' => 1,
   'super_admin' => "super_admin"
   ]);
   session([
   'NickName' => $company_admins->NickName,
   'user_img' => $company_admins->Image,
   'userID' => $company_admins->CompanyAdminID,
   'role' => $company_admins->Rank,
   'fname' => $company_admins->FirstName,
   'lname' => $company_admins->LastName,
   'email' => $company_admins->EmailAddress,
   'department' => $company_admins->Departament,
   'istraining' => $company_admins->isTraining,
   'authorized' => $company_admins->Authorized,
   'privilege' => $company_admins->Privilege,
   'logged' => 1,
   'companyID' => $company_admins->CompanyID,
   'company_admin' => "company_admin",
   ]);


   session([
   'NickName' => $users->NickName,
   'user_img' => $user_img,
   'userID' => $users->UserID,
   'employee' => $users->EmployeeID,
   'EmployeeUUID' => $EmployeeUUID,
   'UserUUID' => $users->UserUUID,
   'email' => $users->EmailAddress,
   'site' => $users->Branch,
   'role' => $users->Rank,
   'fname' => $users->FirstName,
   'lname' => $users->LastName,
   'type' => $users->Type,
   'department' => $users->Departament,
   'istraining' => $users->isTraining,
   'authorized' => $users->Authorized,
   'privilege' => $users->Privilege,
   'approved' => $Approved,
   'logged' => 1,
   'companyID' => $users->CompanyID,
   ]);
   // echo '
   <pre>';
    // print_r($Sulist);
    // echo '</pre>';
   // exit;

   $date = date('Y-m-d');

   // datatalabe
   session('COMPANY_ID')

   $columnsList = ['0' => 'sud.name', '1' => 'l.lang', '2' => 'sud.position'];
   $key = $r->order[0]['column'];
   $direction = $r->order[0]['dir'];
   $columnsName = $columnsList[$key];
   $total = $this->DeclarationCheckListQuery()->count();
   $filter = $total;
   $search = $r->search["value"];
   ->offset($r->start)->limit($r->length)->orderBy($columnsName, $direction)->get()
   return json_encode(["data" => $declaration, "recordsTotal" => $total, "recordsFiltered" => $filter]);


   $key = $request->order[0]->column;
   $colname = $columnsList[$key];
   $dir = $request->order[0]->dir;
   if ($key == 2) {
   $orderby = "CAST(su.room_allocated_id as UNSIGNED) $dir";
   } else {
   $orderby = " $colname $dir ";
   }

   $STORE = $request->STORE;
   $COMPANY = $request->COMPANY;
   $STORE_STAFF = $request->STORE_STAFF;
   $NOTIFY_COUNT = $request->NOTIFY_COUNT;
   $RSS_FEED = $request->RSS_FEED;
   $CHATS_COUNT_Check = $request->CHATS_COUNT;
   if (isset($request->COUNTRY)) {
   $GETCOUNTRY = $request->COUNTRY;
   } else {
   $GETCOUNTRY = "COUNTRY";
   }
   $Location = $request->Location;

   $EmployeeID = $request->EmployeeID;

   $CompanyID = $request->CompanyID;

   $CompanyName = $request->CompanyName;

   $EmployeeName = $request->EmployeeName;
   $EmployeeID = $request->EmployeeID;

   $CompanyID = $request->CompanyID;

   $fromUserId = $request->fromUserId;

   $toUserId = $request->toUserId;

   $toName = $request->toName;

   $fromName = $request->fromName;

   $MessageType = $request->MessageType;

   $Type = $request->Type;

   $Name = $request->Name;

   $ConversationID = $request->ConversationID;


   $Role = $request->Role;

   $UserType = $request->UserType;

   $Type = $request->Type;

   $AdminID = $request->AdminID;

   $GetMenu = $request->GetMenu;

   $GetPendingActivities = $request->GetPendingActivities;

   $Email = $request->Email;

   $UserID = $request->UserID;

   if (isset($request->UserID)) {
   $UserID = $request->UserID;
   }

   return json_encode($success_status);


   if (!empty($CompanyID)) {
   $AND = "AND `CompanyID` = '$CompanyID' ";
   } else {
   $AND = "";
   }

   //Queryiers

   DB::table('app_localstorage')->insert([
   'Role' => $Role,
   'AdminID' => $AdminID,
   'created_date' => $created_date,

   ]);
   ->leftJoin('company_locations as cl', 's.StoreID','=', 'cl.StoreID')




   DB::table('app_localstorage')
   ->where('Role', 'SuperAdmin')
   ->where('AdminID', $AdminID)->update([
   $item => $fetch->value
   ]);
   DB::table('company_admins')->where('EmailAddress', $Email)->where('Inactiv', '0')->exists()
   $rows = DB::table('app_localstorage')->where('UserID', $UserID)->first();


   $.ajax({
   type: 'Post',
   url: '/bulk/delete',
   data: {
   id_s: selected_su,
   dependant: selected_dependant
   },
   dataType: 'JSON',
   success: function(data) {
   SuDatatable();
   },
   });