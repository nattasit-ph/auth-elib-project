<table>
	<thead>
		<tr>
			<th><strong>ชื่อกิจกรรม</strong></th>
			<th><strong>วันที่เริ่มต้น</strong></th>
			<th><strong>วันที่สิ้นสุด</strong></th>
			<th><strong>จำนวนผู้เข้าร่วม</strong></th>
			<th><strong>สถานะ</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->title }}</td>
     		<td>{{ \Carbon\Carbon::parse($rs->event_start)->format('d/m/Y') }}</td>
     		<td>{{ \Carbon\Carbon::parse($rs->event_end)->format('d/m/Y') }}</td>
     		<td>{{ $rs->event_joins_count }}</td>
     		<td>{{ $rs->status == 1 ? 'ใช้งาน' : 'ไม่ใช้งาน' }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>