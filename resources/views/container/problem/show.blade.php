@extends('layouts.project')
@section('title', __('container.problem_with_container'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('container.problem_with_container') }} {{ $container->name }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('container.info_about_problem') }}</h3>
                        </div>
                        <div class="card-body">
                            <b>{{ __('general.container') }}:</b> {{ $container->name }}<br>
                            <b>{{ __('container.size') }}:</b> {{ $container->size }}<br>
                            <b>{{ __('container.owner') }}:</b>
                            {{ $container->type=='В собственности' ? 'В собственности' : optional($container->supplier)->name }}
                            <br><br>
                            <b>{{ __('container.using') }}:</b><br>
                            {{ __('container.start_using') }}: {{ $container->start_date }}<br>
                            {{ __('container.grace_period') }}
                            : {{ $container->grace_period }} {{ __('container.days') }}
                            , {{ __('container.before') }} {{ $usage_dates['end_grace_date'] }}
                            @if ($usage_dates['overdue_days'] != 0)
                                {{ __('container.snp') }} {{ $usage_dates['overdue_days'] }} {{ __('container.days') }}
                            @endif
                            <br>
                            {{ __('container.border_date') }}: {{ $container->border_date }}<br>
                            {{ __('container.svv') }}: {{ __('container.before') }} {{ $usage_dates['svv_date'] }}
                            <div class="mt-4">
                                <b>{{ __('container.problem') }}:</b><br>
                                {{ __('container.problem_type') }}: {{ $container_problem->problem }}<br>
                                {{ __('container.problem_date') }}: {{ $container_problem->problem_date }}<br>
                                {{ __('container.problem_who_fault') }}: {{ $container_problem->who_fault }}<br><br>

                                @if ($container_problem->problem_solved_date !='')
                                    <b>{{ __('container.problem_solved_date') }}
                                        :</b> {{ $container_problem->problem_solved_date }}<br>
                                    <b>{{ __('container.problem_amount') }}:</b> {{ $container_problem->amount }}<br>
                                    <br>
                                @endif
                                <b>{{ __('general.additional_info') }}:</b> {{ $container_problem->additional_info }}
                            </div>
                        </div>
                    </div>

                    @if ($container_problem->problem_solved_date =='')
                        <form action="{{ route('container_problem.update', $container_problem->id) }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="solve_problem">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('container.solve_problem') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="problem">{{ __('container.problem_amount') }}</label>
                                        <input type="text" class="form-control" name="amount"
                                               placeholder="{{ __('container.problem_amount') }} ({{ __('general.with_currency') }})"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label for="problem_photos">{{ __('container.problem_photos_solved') }}</label>
                                        <input type="file" class="form-control-file" name="problem_photos_solved[]"
                                               multiple>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('general.additional_info') }}</label>
                                        <textarea class="form-control" rows="3" name="additional_info"
                                                  placeholder="{{ __('general.additional_info') }}">{{ $container_problem->additional_info }}</textarea>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('container.problem_photos') }}</h3>
                        </div>
                        <div class="card-body">
                            <div id="carouselProblem" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    @php
                                        $i=0;
                                    @endphp
                                    @foreach($problem_files as $file)
                                        @if ($i == 0)
                                            <div class="carousel-item active">
                                                @else
                                                    <div class="carousel-item">
                                                        @endif
                                                        <img class="d-block w-100" src="{{ Storage::url($file) }}">
                                                    </div>
                                                    {{ $i++ }}
                                                    @endforeach
                                                    <a class="carousel-control-prev" href="#carouselProblem"
                                                       role="button" data-slide="prev">
                                                        <span class="carousel-control-prev-icon"
                                                              aria-hidden="true"></span>
                                                        <span class="sr-only">Previous</span>
                                                    </a>
                                                    <a class="carousel-control-next" href="#carouselProblem"
                                                       role="button" data-slide="next">
                                                        <span class="carousel-control-next-icon"
                                                              aria-hidden="true"></span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                            </div>
                                </div>
                            </div>
                        </div>
                        @if ($container_problem->problem_solved_date !='')
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('container.problem_photos_solved') }}</h3>
                                </div>
                                <div class="card-body">
                                    <div id="carouselProblemSolved" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">
                                            @php
                                                $i=0;
                                            @endphp
                                            @foreach($problem_solved_files as $file)
                                                @if ($i == 0)
                                                    <div class="carousel-item active">
                                                        @else
                                                            <div class="carousel-item">
                                                                @endif
                                                                <img class="d-block w-100"
                                                                     src="{{ Storage::url($file) }}">
                                                            </div>
                                                            {{ $i++ }}
                                                        @endforeach
                                                            <a class="carousel-control-prev"
                                                               href="#carouselProblemSolved" role="button"
                                                               data-slide="prev">
                                                                <span class="carousel-control-prev-icon"
                                                                      aria-hidden="true"></span>
                                                                <span class="sr-only">Previous</span>
                                                            </a>
                                                            <a class="carousel-control-next"
                                                               href="#carouselProblemSolved" role="button"
                                                               data-slide="next">
                                                                <span class="carousel-control-next-icon"
                                                                      aria-hidden="true"></span>
                                                                <span class="sr-only">Next</span>
                                                            </a>
                                                    </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                    </div>
                </div>
    </section>
@endsection
