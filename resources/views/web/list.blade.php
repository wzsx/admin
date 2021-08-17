{{--@section('content')--}}
{{--    <div class="container">--}}
{{--        <ul>--}}
{{--            <h2>预约列表</h2>--}}
{{--            @foreach($list as $k=>$v)--}}
{{--                <li>用户：{{$v['user_name']}}  --  手机号：{{$v['tel']}}  --  科室：{{$v['section']}}  --  症状：{{$v['info']}}--}}
{{--                </li>--}}
{{--            @endforeach--}}
{{--        </ul>--}}
{{--        <hr>--}}
{{--    </div>--}}
{{--    {{$list->links()}}--}}
{{--@endsection--}}

{{--@section('footer')--}}
{{--    @parent--}}
{{--@endsection--}}
<body>
@foreach($list as $k=>$v)
                <li>用户：{{$v['user_name']}}  --  手机号：{{$v['tel']}}  --  科室：{{$v['section']}}  --  症状：{{$v['info']}}
                </li>
            @endforeach
{{--<h1>{{$list[0]['user_name']}}</h1>--}}
{{--<p>{{ $introduction }}</p>--}}
</body>
