@extends('layouts.project')
@section('title', __('container.all_containers'))
@section('content')
{{--    <div class="content-header">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="row mb-2">--}}
{{--                <div class="col-sm-12">--}}
{{--                    <h1 class="m-0">--}}
{{--                        @if (isset($_GET['using_now']))--}}
{{--                            {{ __('container.using_now') }}--}}
{{--                            @php--}}
{{--                                $filter = 'using_now';--}}
{{--                            @endphp--}}
{{--                        @elseif (isset($_GET['with_problem']))--}}
{{--                            {{ __('container.with_problem') }}--}}
{{--                            @php--}}
{{--                                $filter = 'with_problem';--}}
{{--                            @endphp--}}
{{--                        @elseif (isset($_GET['own']))--}}
{{--                            {{ __('container.own') }}--}}
{{--                            @php--}}
{{--                                $filter = 'own';--}}
{{--                            @endphp--}}
{{--                        @elseif (isset($_GET['rent']))--}}
{{--                            {{ __('container.in_rent') }}--}}
{{--                            @php--}}
{{--                                $filter = 'rent';--}}
{{--                            @endphp--}}
{{--                        @elseif (isset($_GET['archive']))--}}
{{--                            {{ __('container.in_archive') }}--}}
{{--                            @php--}}
{{--                                $filter = 'archive';--}}
{{--                            @endphp--}}
{{--                        @elseif (isset($_GET['free']))--}}
{{--                            {{ __('container.free') }}--}}
{{--                            @php--}}
{{--                                $filter = 'free';--}}
{{--                            @endphp--}}
{{--                        @else--}}
{{--                            {{ __('container.all_containers') }}--}}
{{--                            @php--}}
{{--                                $filter = 'all';--}}
{{--                            @endphp--}}
{{--                        @endif--}}
{{--                    </h1>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <section class="content">
{{--        <div class="container-fluid">--}}
            @include('layouts.info_block')
        <div id="containers_table_div">
            @include('container.table_extended.table_card_layout', ['table_filter_type' => '', 'application_id' => ''])
        </div>
{{--        </div>--}}
    </section>
@endsection
