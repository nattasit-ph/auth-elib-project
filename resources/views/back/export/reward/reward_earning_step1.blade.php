<table>
	<thead>
		<tr>
			<th><strong>ID</strong></th>
			<th><strong>วันที่ได้รับแต้ม</strong></th>
			<th><strong>ผู้ได้รับแต้ม</strong></th>
			<th><strong>แต้มคะแนน</strong></th>
			<th><strong>ประเภทกิจกรรม</strong></th>
			<th><strong>กิจกรรมที่ทำ</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
			<td>{{$loop->iteration}}</td>
     		<td>{{ \Carbon\Carbon::parse($rs->redeemed_at)->format('d/m/Y') ?? '' }}</td>
     		<td>{{ $rs->user->name }}</td>
     		<td>{{ $rs->point.' x ('.$rs->count.' ครั้ง)' ?? '' }}</td>
     		<td>
				@if ($rs->rewardActivity->module == 'belib_resource')
					ทรัพยากร
				@elseif($rs->rewardActivity->module == 'belib_article')
					บทความ
				@elseif(!$rs->rewardActivity->module)
					{{'KM_'.$rs->rewardActivity->module;}}
				@else
				
				@endif
			</td>
     		<td>{{ $rs->rewardActivity->title ?? '' }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>