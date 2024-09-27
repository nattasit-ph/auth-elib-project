<table>
	<thead>
		<tr>
			<th><strong>ชื่อแบบฟอร์ม</strong></th>
			<th><strong>จำนวนผู้ตอบ</strong></th>
			<th><strong>สถานะ</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->title }}</td>
     		<td>{{ $rs->submissions_count }}</td>
  			<td>{{ $rs->status == 1 ? 'ใช้งาน' : 'ไม่ใช้งาน' }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>