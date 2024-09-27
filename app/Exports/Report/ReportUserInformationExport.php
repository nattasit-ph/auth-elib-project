<?php

namespace App\Exports\Report;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportUserInformationExport implements FromView, ShouldAutoSize, WithTitle
{
	public function __construct($loginDevice = "", $useBrowser = "", $gender = "", $rangeAge = "", $interestTopic = "")
	{
		$this->loginDevice = $loginDevice;
		$this->useBrowser = $useBrowser;
		$this->gender = $gender;
		$this->rangeAge = $rangeAge;
		$this->interestTopic = $interestTopic;
	}

	public function title(): string
	{
		return 'Report - User Information';
	}

	public function view(): View
	{
		$data = [];

		//calculate percentage in pie data
		$namePieData = ['loginDevice', 'useBrowser', 'gender', 'rangeAge'];
		foreach ($namePieData as $item) {
			$totalData = collect($this->{$item})->sum('y');
			$data[$item] = array();
			foreach ($this->{$item} as $value) {
				$value['percentages'] = ($value['y'] / $totalData) * 100;
				array_push($data[$item], $value);
			}
		}

		//calulate percentage in interest topic
		$interestTotal = array_sum($this->interestTopic['data_total']);
		$this->interestTopic['percentages'] = [];
		foreach ($this->interestTopic['data_total'] as $value) {
			$percentages = ($value / $interestTotal) * 100;
			array_push($this->interestTopic['percentages'], $percentages);
		}
		$data['interestTopic'] = $this->interestTopic;

		ob_end_clean();
		return view('back.export.report.user_information', [
			'results' => $data,
		]);
	}
}
