   <?php
   /**
     * function to convert archived leads to studnts
     * 
     * @return  response
     */
    public function convertArchivedToStudents()
    {
        $leads = Lead::where('stage', '=', 'converted')->get();
        $total = 0;
        foreach ($leads as $lead) {
            if ($lead->student_id == null) {
                $exist_with_same_info = Student::where('national_id', '=', $lead->national_id)->first();
                if (!empty($exist_with_same_info)) {
                    $exist_same_email = Student::where('email', '=', $lead->email)->first();
                    $student = new Student;
                    $student->first_name = $lead->first_name;
                    $student->father_name = $lead->father_name;

                    if (empty($exist_same_email)) {
                        $student->email = $lead->email;
                    }
                    
                    $student->class = $lead->class;
                    $student->mobile = $lead->mobile;
                    $student->date_of_birth = $lead->date_of_birth;
                    $student->gender = $lead->gender;
                    $student->school_id = $lead->school_id;
                    $student->address = $lead->address;
                    $student->phone = $lead->phone;
                    $student->save();

                    $new_participation = new Participation;
                    $new_participation->student_id = $student->id;
                    $new_participation->activity_id = $lead->activity_id;
                    $new_participation->save();

                    $orders_number = '';
                    $activity_cost = 0;
                    
                    try {
                        $activity = Activity::find($lead->activity_id);
                        if (!empty($activity)) {
                            $activity_cost = $activity->cost;
                        } else {
                            $activity_cost = 0;
                        }
                        
                    } catch (ModelNotFoundException $e) {
                        return response()->json([
                            'error' => 'no activity with this id : '.$lead->activity_id
                        ]);
                    }
                    
                    $orders =  DB::table('stduent_account')
                        ->where('payment_type','order')
                        ->orderby('payment_number', 'desc')
                        ->first();

                    if( is_null($orders) ) {
                        $oreders_number = 100000;
                    } else {
                        $oreders_number = $orders->payment_number;
                    }
                    
                    $student_account  = Student_Account::create([
                        'payment_cost' =>  $activity_cost,
                        'payment_number' => ($oreders_number + 1),
                        'payment_type' => 'order',
                        'payment_description' => '-',
                        'payment_date' => date('Y-m-d'),
                        'student_id' => $student->id,
                        'activity_id' => $lead->activity_id
                    ]);
                    $student_account->save();

                    // update lead ststus 
                    $lead = Lead::find($lead->id)->update([
                        'stage' => 'converted',
                        'student_id' => $student->id
                    ]);
                } else {
                   
                }
            }
        }
    }