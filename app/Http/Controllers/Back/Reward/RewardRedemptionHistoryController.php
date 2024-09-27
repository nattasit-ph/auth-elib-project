<?php

namespace App\Http\Controllers\Back\Reward;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\RewardItem;
use App\Models\RewardRedemptionHistory;
use App\Models\User;
use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Reward\ReportRewardRedemptionHistoryExport;

class RewardRedemptionHistoryController extends BackController
{
	public function __construct()
 	{
 		$this->middleware(function ($request, $next) {
			parent::getSiteConfig();

        	$this->user = Auth::user();
    		if ($this->user->hasAnyRole(['Super Admin Belib', 'Admin Belib', 'Super Admin Learnext', 'Admin Learnext', 'Super Admin KM', 'Admin KM'])) {
    			return $next($request);
 			}
 			else {
 				return redirect()->route('home');
 			}
		});
 	}

	public function index()
	{
        $org_slug = Auth::user()->org->slug;

		return view('back.'.config('bookdose.theme_back').'.modules.reward.reward_redemption.list', compact('org_slug'));
	}

	public function ajaxGetData(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$query = RewardRedemptionHistory::with(['rewardItem.rewardGalleries', 'user'])
				->select(
	    			array(
	    				'*',
	    				DB::raw('DATE_FORMAT(reward_redemption_histories.redeemed_at, "%d/%m/%Y %H:%i") AS redeemed_date'),
	    			)
	    		);

		$delivery_status = $request->delivery_status ?? '';
		if (in_array($delivery_status, ['0', '1'])) {
			$query = $query->where('is_delivered', $delivery_status);
		}

		$refund_status = $request->refund_status ?? '';
		if (in_array($refund_status, ['0', '1'])) {
			$query = $query->where('is_refunded', $refund_status);
		}

		$datatable = new Datatables;
		return $datatable
			->eloquent($query)
			->addColumn('reward_item_title', function ($history) {
				return $history->rewardItem->title;
			})
			->addColumn('user_fullname', function ($history) {
				return $history->user->name;
			})
			->addColumn('delivery_status_html', function ($history) {
				if ($history->is_delivered == 1)
					return '<span class="kt-badge kt-badge--success kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-success">Delivered</span>';
				else
					return '<span class="kt-badge kt-badge--danger kt-badge--dot"></span>&nbsp;<span class="kt-font-bold kt-font-danger">Not delivered</span>';
			})
			->addColumn('refund_status_html', function ($history) {
				if ($history->is_refunded == 1)
					return Carbon::parse($history->refunded_at)->format('d M Y H:i');
				else
					return '';
			})
			->addColumn('actions', function ($history) {
				$menu_status = '';
				$menu_refund = '';
				if ($history->is_delivered == 1) {
					$menu_status = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($history->id).' data-status='.json_encode($history->is_delivered).' data-reward-title='.json_encode($history->rewardItem->title, JSON_UNESCAPED_UNICODE).' data-user-fullname='.json_encode($history->user->name, JSON_UNESCAPED_UNICODE).' onClick="toggleDeliveryStatus(this)"><i class="fas fa-truck"></i> Mark as <span class="text-danger ml-2">Not Delivered</span></a>';
				}
				else {
					if ($history->is_refunded == 0) {
						$menu_status = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($history->id).' data-status='.json_encode($history->is_delivered).' data-reward-title='.json_encode($history->rewardItem->title, JSON_UNESCAPED_UNICODE).' data-user-fullname='.json_encode($history->user->name, JSON_UNESCAPED_UNICODE).' onClick="toggleDeliveryStatus(this)"><i class="fas fa-truck-loading"></i> Mark as <span class="text-success ml-2">Delivered</span></a>';
					}
					$menu_refund = '<a class="dropdown-item" href="javascript:;" data-id='.json_encode($history->id).' data-status='.json_encode($history->is_delivered).' data-point='.json_encode($history->total_point, JSON_UNESCAPED_UNICODE).' data-user-fullname='.json_encode($history->user->name, JSON_UNESCAPED_UNICODE).' onClick="refund(this)"><i class="fas fa-hand-holding"></i> Refund '.$history->total_point.' points</a>';
				}

				if (!empty($menu_status) || !empty($menu_delete)) {
					return '
					<span class="dropdown">
						<a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
							<i class="la la-ellipsis-h"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							'.$menu_status.'
							'.$menu_refund.'
						</div>
					</span>';
				}
				return '';
			})
			->rawColumns(['reward_item_title', 'delivery_status_html', 'actions'])
			->addIndexColumn()
			->make(true);
	}

	public function ajaxRefund(Request $request)
	{
        $org_slug = Auth::user()->org->slug;

		$id = $request->input('id');
		$history = RewardRedemptionHistory::find($id);
		if ($history) {
			$update_data = array(
				'is_refunded' => 1,
				'refunded_at' => now(),
				'refunded_by' => Auth::user()->id,
				'updated_by' => Auth::user()->id,
			);
			$rs = RewardRedemptionHistory::where('id', $id)->update($update_data);
			if ($rs) {
				// Add points back to user
				User::where('id', $history->user_id)->update([
					'points' => DB::raw('points + '.$history->total_point),
					'updated_by' => Auth::user()->id,
				]);

				// Add stock_avail back
				RewardItem::where('id', $history->reward_item_id)->increment('stock_avail');

				return json_encode(array(
					'status' => 200,
					'notify_title' => 'Hooray!',
					'notify_msg' => 'This transaction has been refunded successfully.',
					'notify_icon' => 'icon la la-check-circle',
					'notify_type' => 'success',
				));
			}
		}
		return json_encode(array(
			'status' => 500,
			'notify_title' => 'Oops!',
			'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
			'notify_icon' => 'icon la la-warning',
			'notify_type' => 'danger',
		));
	}

	public function ajaxSetDeliveryStatus(Request $request)
	{
		$id = $request->input('id');
		if ($id > 0) {
			if ($request->input('status') == '0') {
				$update_data = array(
					'is_delivered' => 1,
					'delivered_at' => now(),
					'delivered_by' => Auth::user()->id,
					'updated_by' => Auth::user()->id,
				);
			}
			else {
				$update_data = array(
					'is_delivered' => 0,
					'delivered_at' => NULL,
					'delivered_by' => NULL,
					'updated_by' => Auth::user()->id,
				);
			}
			$rs = RewardRedemptionHistory::where('id', $id)->update($update_data);
			if ($rs) {
				return json_encode(array(
					'status' => 200,
					'notify_title' => 'Hooray!',
					'notify_msg' => 'Delivery status has been updated successfully.',
					'notify_icon' => 'icon la la-check-circle',
					'notify_type' => 'success',
				));
			}
		}
		return json_encode(array(
			'status' => 500,
			'notify_title' => 'Oops!',
			'notify_msg' => 'Something went wrong. Please refresh this page and then try again.',
			'notify_icon' => 'icon la la-warning',
			'notify_type' => 'danger',
		));
	}

  public function exportToExcel(Request $request)
  {
		$is_delivered = $request->input('hd_is_delivered');
		$is_refunded = $request->input('hd_is_refunded');
		$keyword = $request->input('hd_keyword');
	  	// dd($is_delivered, $is_refunded, $keyword);
		return Excel::download(new ReportRewardRedemptionHistoryExport($is_delivered, $is_refunded, $keyword), 'report_redemption_history_'.now().'.xlsx');
  }

}
