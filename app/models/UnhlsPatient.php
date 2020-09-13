<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class UnhlsPatient extends Eloquent
{
	const MALE = 0;
	const FEMALE = 1;
	const BOTH = 2;
	const UNKNOWN = 3;
	const MICRO = 1;
	const NOTMICRO = 0;
	/**
	 * Enabling soft deletes for patient details.
	 *
	 */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];
    	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'unhls_patients';

	/**
	 * Visits relationship
	 */
    public function visits()
    {
        return $this->hasMany('UnhlsVisit', 'patient_id');
    }
/**
	 * Visits relationship
	 */
    public function microDetails()
    {
        return $this->hasOne('MicroPatientDetail', 'patient_id');
    }


    public static function getAllPatients(){
    	$sql = "select * from unhls_patients";
    	$patients = DB::select($sql);

    	return $patients;
    }
    /**
	 * District relationship
	 */
    public function district()
	{
		return $this->belongsTo('District', 'id');
	}

    
	/**
	 * Patient Age 
	 *
	 * @param optional String - format [Y|YY|YYMM]
	 * @param optional Timestamp - age as at this date
	 * @return String x years y months
	 */
	public function getAge($format = "YYMM", $at = NULL)
	{
		if(!$at)$at = new DateTime('now');

		$dateOfBirth = new DateTime($this->dob);
		$interval = $dateOfBirth->diff($at);		
		$age = "";

		switch ($format) {
			case 'ref_range_Y':
				$seconds = ($interval->days * 24 * 3600) + ($interval->h * 3600) + ($interval->i * 60) + ($interval->s);
				$age = $seconds/(365*24*60*60);
				break;
			case 'Y':
			    
				$age = $interval->y;break;
			case 'YY':
			    
				$age = $interval->y ." years ";break;
			default:
				if($interval->y == 0){
					
					
					if($interval->format('%a') > 31){
						$age = $interval->format('%m months');
					}else{
						$age = $interval->format('%a days');
					 }
				}
				elseif($interval->y > 0 && $interval->y <= 2){
				
					$age = $interval->format('%m') + 12 * $interval->format('%y')." months";
				}
				else{
					
					$age=$interval->y." years ";

					$seconds = ($interval->days * 24 * 3600) + ($interval->h * 3600) + ($interval->i * 60) + ($interval->s);
				    $age = round($seconds/(365*24*60*60), 1)." years ";
				}
				
				break;
		}

		return $age;
	}

	public function newAge($dateOfBirth){

		$new_age = "";
		$at = new DateTime('now');

		$dateOfBirth = new DateTime($dateOfBirth);
		$interval = $dateOfBirth->diff($at);

		$days = $interval->format('%a');

		if($days < 30){
			$new_age = $days . " days";

		}elseif ($days >= 30 && $days < 365) {
			$months = round($days/12);
			$new_age = $months . " month(s)";
		}else{
			$years = round($days/365);
			$new_age = $years . " year(s)";
		}
		return $new_age;
	}
	/**
	* Get patient's gender
	*
	* @param optional boolean $shortForm - return abbreviation (M/F). Default true
	* @return String gender 
	*/
	public function getGender($shortForm=true)
	{
		if ($this->gender == UnhlsPatient::MALE)
		{
			return $shortForm?"M":trans("messages.male");
		}
		else if ($this->gender == UnhlsPatient::FEMALE)
		{
			return $shortForm?"F":trans("messages.female");
		}
	}

	/**
	* Search for patients meeting given criteria
	*
	* @param String $searchText
	* @return Collection 
	*/
	public static function search($searchText)
	{
		return UnhlsPatient::where('patient_number', '=', $searchText)
						->orWhere('name', 'LIKE', '%'.$searchText.'%')
						->orWhere('ulin', 'LIKE', '%'.$searchText.'%')
						->orWhere('external_patient_number', '=', $searchText);
	}
	/**
	* Get patients facility Id Number
	*
	*/
	public function getFacilityCode()
	{
		$facilityCode =\Config::get('constants.FACILITY_CODE');
		return $facilityCode;
	
	}

	/**
    * Get patients Unique Identification Number (ULIN)
    *
    * @return string
    */
    public function getUlin(){

		$format = AdhocConfig::where('name','ULIN')->first()->getULINFormat();
		$facilityCode ='';
		$facilityCode = $this->getFacilityCode();
		$registrationDate = strtotime($this->created_at);
		if ($format == 'Jinja_SOP') {
			$lastPatientRegistration = UnhlsPatient::orderBy('id','DESC')->first()->created_at;
			$monthOfLastEntry = date('m',strtotime($lastPatientRegistration));
			$monthNow = date('m');

			if ($monthOfLastEntry != $monthNow) {
				Artisan::call('reset:ulin');
			}

			$year = date('y', $registrationDate);
			$month = date('m', $registrationDate);
			$autoNum = DB::table('uuids')->max('id')+1;
			return $autoNum.'/'.$month.'/'.$year;

		}elseif ($format == 'Mityana_SOP') {
			$lastPatientRegistration = UnhlsPatient::orderBy('id','DESC')->first()->created_at;
			$monthOfLastEntry = date('m',strtotime($lastPatientRegistration));
			$monthNow = date('m');

			if ($monthOfLastEntry != $monthNow) {
				Artisan::call('reset:ulin');
			}

			$year = date('y', $registrationDate);
			$month = date('m', $registrationDate);
			$autoNum = DB::table('uuids')->max('id')+1;


			$name = preg_split("/\s+/", trim($this->name));
			$initials = null;

			foreach ($name as $n){
				$initials .= $n[0];

			}
			return $initials.'/'.$month.'/'.$autoNum.'/'.$year;
			// MG/12/220/17
		}elseif($format == 'Rukunyu_SOP'){
			$yearMonth = date('m/y', $registrationDate);
			$autoNum = DB::table('uuids')->max('id')+1;
			return $autoNum.'/'.$yearMonth;
		}
		else{
			$yearMonth = date('ym', $registrationDate);
			$autoNum = DB::table('uuids')->max('id')+1;
			$name = preg_split("/\s+/", trim($this->name));
			$initials = null;

			foreach ($name as $n){
				$initials .= $n[0];

			}
			return $facilityCode.'/'.$yearMonth.'/'.$autoNum.'/'.$initials;
		}
    }
    public function isMicro(){
    	if($this->is_micro == UnhlsPatient::MICRO)
			return true;
		else 
			return false;
    }
    public function isNotMicro(){
    	if($this->is_micro == UnhlsPatient::NOTMICRO)
			return true;
		else 
			return false;
    }
}