<lable>已回款</lable>
<table>
    <tr>
        <td>项目信息</td>
        <td>投资日期</td>
        <td>出借金额</td>
        <td>到期日期</td>
        <td>回款总额</td>
        <td>操作</td>
    </tr>
    @foreach($list as $record)
        <tr>
            <td>{{ $record['name'] }} {{ $record['profit_percentage'] }} {{ $record['invest_time'] }}</td>
            <td>{{ $record['created_at'] }}</td>
            <td>{{ $record['principal'] }}</td>
            <td>{{ $record['end_at'] }}</td>
            <td>{{ $record['total'] }}</td>
            <td>回款计划</td>
        </tr>
    @endforeach
</table>
<lable>总页数：{{ $page }} 分页：
    @for ($i = 1; $i <= $page; $i++)
        {{ $i }}
    @endfor
</lable>