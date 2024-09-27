<?php

namespace App\Http\Controllers\Back\Report;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Back\BackController;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LoginHistory;
use App\Models\Interested;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Report\ReportUserInformationExport;
use App\Models\VisitorLog;

class UserReportController extends BackController
{
	public function __construct()
	{
		$this->middleware(function ($request, $next) {
			parent::getSiteConfig();

			$this->user = Auth::user();
			if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
				return $next($request);
			} else {
				return redirect()->route('home');
			}
		});
	}

	public function overallForm()
	{
		return view('back.' . config('bookdose.theme_back') . '.modules.report.user.overall');
	}

	public function ajaxGetGender()
	{
		$data_total = $this->getGender();
		$tmp = array();
		$tmp["name"] = "#Total";
		$tmp["type"] = "pie";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;

		$return = array('data_chart' => $data_chart);
		echo json_encode($return);
		return;
	}

	public function ajaxGetRangeAge()
	{
		$data_total = $this->getRangeAge();
		$tmp = array();
		$tmp["name"] = "#Total";
		$tmp["type"] = "pie";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;

		$return = array('data_chart' => $data_chart);
		echo json_encode($return);
		return;
	}

	public function ajaxGetUserInterestTopic()
	{
		$data = $this->getInterestTopic();
		$tmp = array();
		$tmp["name"] = "#Total";
		$tmp["type"] = "column";
		$tmp["data"] = $data['data_total'];
		$data_chart[] = $tmp;

		$return = array('data_chart' => $data_chart, 'task_title' => $data['task_title']);
		echo json_encode($return);
		return;
	}

	public function ajaxGetLoginDevice()
	{
		$data_total = $this->getLoginDevice();
		$tmp = array();
		$tmp["name"] = "#Total";
		$tmp["type"] = "pie";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;

		$return = array('data_chart' => $data_chart);
		echo json_encode($return);
		return;
	}

	public function ajaxGetUsuallyBrowser()
	{
		$data_total = $this->getUsuallyBrowser();
		$tmp = array();
		$tmp["name"] = "#Total";
		$tmp["type"] = "pie";
		$tmp["data"] = $data_total;
		$data_chart[] = $tmp;

		$return = array('data_chart' => $data_chart);
		echo json_encode($return);
		return;
	}

	public function exportToExcel()
	{
		$loginDevice = $this->getLoginDevice();
		$useBrowser = $this->getUsuallyBrowser();
		$gender = $this->getGender();
		$rangeAge = $this->getRangeAge();
		$interestTopic = $this->getInterestTopic();

		return Excel::download(new ReportUserInformationExport($loginDevice, $useBrowser, $gender, $rangeAge, $interestTopic), 'report_user_information_' . now() . '.xlsx');
	}

	private function getGender()
	{
		$result = User::myOrg()->Active()->get();
		$data_total = [];
		if (!empty($result)) {

			//male
			$male = $result->where('gender', 'm');
			$obj = array();
			$obj["name"] = 'Male';
			$obj["y"] = $male->count() ?? 0;
			$data_total[] = $obj;

			//female
			$female = $result->where('gender', 'f');
			$obj = array();
			$obj["name"] = 'Female';
			$obj["y"] = $female->count() ?? 0;
			$data_total[] = $obj;

			//unspecified
			$unspecified = $result->whereNotIn('gender', ['m', 'f']);
			$obj = array();
			$obj["name"] = 'Unspecified';
			$obj["y"] = $unspecified->count() ?? 0;
			$data_total[] = $obj;
		}
		return $data_total;
	}

	private function getRangeAge()
	{
		$result = User::myOrg()->Active()->get();
		$data_total = [];
		if (!empty($result)) {
			$data_temp = [];
			$data_name = [];
			foreach ($result as $value) {
				if (isset($value->data_info['range_age'])) {
					if (isset($data_temp[$value->data_info['range_age']])) {
						$data_temp[$value->data_info['range_age']] += 1;
					} else {
						$data_temp[$value->data_info['range_age']] = 1;
						array_push($data_name, $value->data_info['range_age']);
					}
				}
			}
			$count = 0;
			foreach ($data_name as $value) {
				$obj = array();
				$obj["name"] = $value;
				$obj["y"] = $data_temp[$value] ?? 0;
				$data_total[] = $obj;
				$count += $data_temp[$value] ?? 0;
			}
			if ($count < $result->count()) {
				$obj = array();
				$obj["name"] = 'Unspecified';
				$obj["y"] = $result->count() - $count;
				$data_total[] = $obj;
			}
		}

		return $data_total;
	}

	private function getInterestTopic()
	{
		$task_title = [];
		$user = User::myOrg()->Active()->get();
		$interestTopic = Interested::MyOrg()->Active()->get();
		$data_total = [];
		if (!empty($interestTopic)) {
			foreach ($interestTopic as $item) {
				$task_title[] = $item->title ?? '';
				$count = 0;
				foreach ($user as $value) {
					$user_intested = json_decode($value->data_interested);
					if (!is_null($user_intested)) {
						if (array_search($item->id, $user_intested) !== false) {
							$count += 1;
						}
					}
				}
				$data_total[] = $count;
			}
		}

		return array('data_total' => $data_total, 'task_title' => $task_title);
	}

	public function getLoginDevice()
	{
		$loginHistory = LoginHistory::myOrg()->select('login_histories.*', DB::raw('count(*) as total'))->groupBy('device')->get();
		$data_total = [];
		if (!empty($loginHistory)) {
			foreach ($loginHistory as $item) {
				$obj = array();
				$obj["name"] = $item->device ?? 'N/A';
				$obj["y"] = $item->total ?? 0;
				$data_total[] = $obj;
			}
		}

		return $data_total;
	}

	private function getUsuallyBrowser()
	{
		$data_total = [];
		$visitlog = VisitorLog::get()->each(function ($item) {
			$browser = 'Unknown';
			if (preg_match('/Firefox/i', $item->browser)) $browser = 'Firefox';
			elseif (preg_match('/Mac/i', $item->browser)) $browser = 'Mac';
			elseif (preg_match('/Chrome/i', $item->browser)) $browser = 'Chrome';
			elseif (preg_match('/Opera/i', $item->browser)) $browser = 'Opera';
			elseif (preg_match('/MSIE/i', $item->browser)) $browser = 'IE';
			$item->browser = $browser;
		});
		$visitlog = $visitlog->groupBy('browser');
		foreach ($visitlog as $value) {
			$obj = array();
			$obj["name"] = $value[0]->browser ?? 'N/A';
			$obj["y"] = $value->count() ?? 0;
			$data_total[] = $obj;
		}

		return $data_total;
	}
}
