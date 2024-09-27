<table>
	<thead>
		<tr>
			<th><strong>Login Device (อุปกรณ์ที่ใช้เข้าสู่ระบบ)</strong></th>
		</tr>
	</thead>
	<thead>
		<tr>
			<th><strong>Device</strong></th>
			<th style="text-align: center;"><strong>total</strong></th>
			<th style="text-align: center;"><strong>%</strong></th>
		</tr>
	</thead>
	<tbody>
		@foreach($results['loginDevice'] as $value)
		<tr>
			<td>{{ $value['name'] }}</td>
			<td style="text-align: center;">{{ $value['y'] }}</td>
			<td style="text-align: center;">{{ round( $value['percentages'], 1) . '%' }}</td>
		</tr>
		@endforeach
	</tbody>
</table>


<table>
	<thead>
		<tr>
			<th><strong>Usually Use Browser (เบราว์เซอร์ที่เข้าใช้งาน)</strong></th>
		</tr>
	</thead>
	<thead>
		<tr>
			<th><strong>Browser</strong></th>
			<th style="text-align: center;"><strong>total</strong></th>
			<th style="text-align: center;"><strong>%</strong></th>
		</tr>
	</thead>
	<tbody>
		@foreach($results['useBrowser'] as $value)
		<tr>
			<td>{{ $value['name'] }}</td>
			<td style="text-align: center;">{{ $value['y'] }}</td>
			<td style="text-align: center;">{{ round( $value['percentages'], 1) . '%' }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

<table>
	<thead>
		<tr>
			<th><strong>User Gender (สถิติของผู้ใช้งานตามเพศ)</strong></th>
		</tr>
	</thead>
	<thead>
		<tr>
			<th><strong>Gender</strong></th>
			<th style="text-align: center;"><strong>total</strong></th>
			<th style="text-align: center;"><strong>%</strong></th>
		</tr>
	</thead>
	<tbody>
		@foreach($results['gender'] as $value)
		<tr>
			<td>{{ $value['name'] }}</td>
			<td style="text-align: center;">{{ $value['y'] }}</td>
			<td style="text-align: center;">{{ round( $value['percentages'], 1) . '%' }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

<table>
	<thead>
		<tr>
			<th><strong>User Range Age (สถิติของผู้ใช้งานตามช่วงอายุ)</strong></th>
		</tr>
	</thead>
	<thead>
		<tr>
			<th><strong>Range Age</strong></th>
			<th style="text-align: center;"><strong>total</strong></th>
			<th style="text-align: center;"><strong>%</strong></th>
		</tr>
	</thead>
	<tbody>
		@foreach($results['rangeAge'] as $value)
		<tr>
			<td>{{ $value['name'] }}</td>
			<td style="text-align: center;">{{ $value['y'] }}</td>
			<td style="text-align: center;">{{ round( $value['percentages'], 1) . '%' }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

<table>
	<thead>
		<tr>
			<th><strong>User Interest Topic (หมวดหมู่ที่ผู้ใช้งานสนใจ)</strong></th>
		</tr>
	</thead>
	<thead>
		<tr>
			<th><strong>หมวดหมู่</strong></th>
			<th style="text-align: center;"><strong>total</strong></th>
			<th style="text-align: center;"><strong>%</strong></th>
		</tr>
	</thead>
	<tbody>
		@foreach($results['interestTopic']['task_title'] as $key => $value)
		<tr>
			<td>{{ $value }}</td>
			<td style="text-align: center;">{{ $results['interestTopic']['data_total'][$key] }}</td>
			<td style="text-align: center;">{{ round( $results['interestTopic']['percentages'][$key], 1) . '%' }}</td>
		</tr>
		@endforeach
	</tbody>
</table>