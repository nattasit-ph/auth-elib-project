<table>
	<thead>
		<tr>
			<th><strong>วันที่แลก</strong></th>
         <th><strong>ผู้แลกของรางวัล</strong></th>
         <th><strong>รายชื่อของรางวัล</strong></th>
         <th><strong>จำนวน</strong></th>
         <th><strong>แต้มที่ใช้แลก</strong></th>
         <th><strong>สถานะการรับของรางวัล</strong></th>
         <th><strong>วัน/เวลารับของรางวัล</strong></th>
         <th><strong>วัน/เวลาคืนแต้ม</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ \Carbon\Carbon::parse($rs->redeemed_at)->format('d/m/Y') ?? '' }}</td>
     		<td>{{ $rs->user->member_id.' - '.$rs->user->name }}</td>
     		<td>{{ $rs->rewardItem->title ?? '' }}</td>
     		<td>{{ $rs->unit ?? '' }}</td>
     		<td>{{ $rs->total_point ?? '' }}</td>
     		<td>{{ $rs->is_delivered==1 ? 'รับของรางวัลแล้ว' : 'ยังไม่รับของรางวัล' }}</td>
     		<td>{{ !empty($rs->delivered_at) ? \Carbon\Carbon::parse($rs->delivered_at)->format('d/m/Y H:i') : '' }}</td>
     		<td>{{ !empty($rs->refunded_at) ? \Carbon\Carbon::parse($rs->refunded_at)->format('d/m/Y H:i') : '' }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>