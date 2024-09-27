<table>
	<thead>
		<tr>
			<th><strong>ชื่อห้อง</strong></th>
			<th><strong>วันที่จอง</strong></th>
			<th><strong>เวลาเริ่ม</strong></th>
			<th><strong>เวลาสิ้นสุด</strong></th>
			<th><strong>หัวข้อ</strong></th>
			<th><strong>ชื่อผู้จอง</strong></th>
			<th><strong>อีเมล</strong></th>
			<th><strong>สถานะ</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->room_title }}</td>
     		<td>{{ date('d/m/Y', strtotime($rs->start_datetime)) ?? '' }}</td>
			<td>{{ date('H:i', strtotime($rs->start_datetime)) ?? '' }}</td>
			<td>{{ date('H:i', strtotime($rs->end_datetime)) ?? '' }}</td>
			<td>{{ $rs->title }}</td>
     		<td>{{ $rs->user_name }}</td>
     		<td>{{ $rs->email ?? '' }}</td>
			<td>{{ $rs->status == 1 ? 'จอง':'ยกเลิก' }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>