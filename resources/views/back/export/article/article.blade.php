<table>
	<thead>
		<tr>
			<th><strong>ชื่อข่าว</strong></th>
            <th><strong>หมวดหมู่</strong></th>
			<th><strong>บทคัดย่อ</strong></th>
			<th><strong>จำนวนผู้เข้าชม</strong></th>
			<th><strong>จำนวนผู้แชร์</strong></th>
            <th><strong>จำนวนผู้ถูกใจ</strong></th>
            <th><strong>จำนวนคอมเม้นต์</strong></th>
            <th><strong>ผู้แผยแพร่</strong></th>
            <th><strong>อีเมลผู้เผยแพร่</strong></th>
            <th><strong>รายการแนะนำ</strong></th>
			<th><strong>สถานะ</strong></th>
            <th><strong>วันที่เผยแพร่</strong></th>
	   </tr>
   </thead>

   <tbody>
    	@foreach($results as $rs)
     	<tr>
     		<td>{{ $rs->title }}</td>
            @php $categoriesArr = array(); 
            foreach ($rs->categories as $value) {
                $categoriesArr[] = $value->title;
            }
            $categories = implode(",", $categoriesArr);
            @endphp 
            <td>{{ $categories }}</td>
     		<td>{{ $rs->excerpt ?? '-'}}</td>
     		<td>{{ $rs->total_view ?? '0'}}</td>
     		<td>{{ $rs->total_share ?? '0'}}</td>
			<td>{{ $rs->total_action ?? '0'}}</td>
            <td>{{ $rs->comments_count ?? '0'}}</td>
            <td>{{ $rs->creator->name ?? '' }}</td>
            <td>{{ $rs->creator->email ?? '' }}</td>
            <td>{{ $rs->is_recommended == 1 ? 'Yes':'No' }}</td>
            <td>{{ $rs->status == 1 ? 'Active':'Inactivate' }}</td>
            <td>{{ Carbon\Carbon::parse($rs->published_at)->format('d/m/Y') ?? '-' }}</td>
		</tr>
    	@endforeach
    </tbody>
</table>