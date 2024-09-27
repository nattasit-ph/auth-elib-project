<table>
	<thead>
		<tr>
			<th><strong>Username</strong></th>
			<th><strong>ชื่อ-สกุล</strong></th>
			<th><strong>วันที่ส่งคำเชิญ</strong></th>
			<th><strong>วันที่ลงทะเบียนเข้าร่วมกิจกรรม</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->user->username }}</td>
     		<td>{{ $rs->user->name }}</td>
     		<td>{{ $rs->invited_date }}</td>
     		<td>{{ $rs->joined_date }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>