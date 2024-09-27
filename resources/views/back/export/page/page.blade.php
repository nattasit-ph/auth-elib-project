<table>
	<thead>
		<tr>
			<th><strong>ชื่อเว็บเพจ (TH)</strong></th>
			<th><strong>ชื่อเว็บเพจ (EN)</strong></th>
            <th><strong>Pretty URL</strong></th>
			<th><strong>จำนวนผู้เข้าชม</strong></th>
			<th><strong>จำนวนผู้แชร์</strong></th>
			<th><strong>สถานะ</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->title_th }}</td>
			<td>{{ $rs->title_en }}</td>
            <td>{{ $rs->slug }}</td>
     		<td>{{ $rs->total_view ?? '0'}}</td>
     		<td>{{ $rs->total_share ?? '0'}}</td>
            <td>{{ $rs->status == 1 ? 'Active':'Inactivate' }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>