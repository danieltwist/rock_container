@extends('layouts.project')

@section('title', __('container.choose_containers_for_project'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('container.choose_containers_for_project') }} {{ $project->name }} <a
                            href="{{ route('project.show', $project['id']) }}"
                            class="btn btn-default">
                            {{ __('project.back_to_project') }}
                            </a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{ $project->id }}" id="project_id">
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div id="success_ajax">
            </div>
            @if (canWorkWithProject($project->id))
                @can ('work with projects')
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('container_group_upload_list') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('container.upload_list') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>{{ __('container.add_to_group_or_add_new') }}</label>
                                            <select class="form-control" name="type" style="width: 100%;" id="container_group_add_type_list">
                                                <option value="new_group">{{ __('container.new_group') }}</option>
                                                <option value="add_to_group">{{ __('container.add_to_group') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="container_new_group_list">
                                            <label for="name">{{ __('container.group_name') }}</label>
                                            <input type="text" class="form-control group_name" name="name" placeholder="{{ __('container.group_name') }}" required>
                                        </div>
                                        <div class="form-group d-none" id="add_to_group_list">
                                            <label>{{ __('container.choose_list') }}</label>
                                            <select class="select2 chosen_group_to_add" data-placeholder="{{ __('container.choose_list') }}" name="chosen_group_to_add" style="width: 100%;">
                                                <option></option>
                                                @foreach($container_groups as $group)
                                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="file">{{ __('general.choose_file') }}</label>
                                            <input type="file" class="form-control-file" name="containers_list">
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary float-right">{{ __('general.save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('general.info_about_using') }}</h3>
                                    <div class="card-tools">
                                        <a href="/storage/templates/containers_group_template.xlsx" download
                                           class="btn btn-block btn-success btn-xs">{{ __('general.download_template') }}</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>{{ __('container.info_about_using_1') }}</li>
                                        <li>{{ __('container.info_about_using_2') }}</li>
                                        <li>{{ __('container.info_about_using_3') }}</li>
                                        <li>{{ __('container.info_about_using_4') }} <a href="{{ route('supplier.index')}}" target="_blank">{{ __('general.all_suppliers') }}</a>, {{ __('container.info_about_using_5') }}</li>
                                        <li>{{ __('container.info_about_using_6') }}</li>
                                        <li>{{ __('container.info_about_using_7') }}</li>
                                        <li>{{ __('container.info_about_using_8') }}</li>
                                        <li>{{ __('container.info_about_using_9') }}</li>
                                        <li>{{ __('container.info_about_using_11') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('project.container_group.store',$project->id) }}" method="POST">
                            @csrf
                            <div class="card collapsed">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('container.choose_containers_manually') }}</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>{{ __('container.add_to_group_or_add_new') }}</label>
                                        <select class="form-control" name="type" style="width: 100%;" id="container_group_add_type">
                                            <option value="new_group">{{ __('container.new_group') }}</option>
                                            <option value="add_to_group">{{ __('container.add_to_group') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="container_new_group">
                                        <label for="name">{{ __('container.group_name') }}</label>
                                        <input type="text" class="form-control group_name" name="name" placeholder="{{ __('container.group_name') }}" required>
                                    </div>
                                    <div class="form-group" id="add_to_group" style="display: none">
                                        <label>{{ __('container.choose_list') }}</label>
                                        <select class="select2 chosen_group_to_add" data-placeholder="{{ __('container.choose_list') }}" name="chosen_group_to_add" style="width: 100%;">
                                            <option></option>
                                            @foreach($container_groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('container.available_containers_list') }}</label>
                                        <select class="select2" multiple="multiple" data-placeholder="{{ __('container.choose_containers') }}" name="chosen_containers[]" style="width: 100%;" required>
                                            @foreach($all_containers as $container)
                                                <option value="{{ $container->id }}">{{ $container->name }} - {{ $container->type == 'Аренда' ? optional($container->supplier)->name : __('container.own') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('general.additional_info') }}</label>
                                        <textarea class="form-control" rows="3" name="additional_info" placeholder="{{ __('general.additional_info') }}"></textarea>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary float-right">{{ __('general.save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('container.groups_for_this_project') }}</h3>
                            </div>
                            <div class="card-body">
                                @if ($container_groups->isEmpty())
                                    {{ __('container.containers_not_chosen') }}
                                @else
                                    @foreach($container_groups as $group)
                                        <b>{{$group->name}}:</b><br>
                                        <table class="table table-striped containers_group">
                                            <thead>
                                            <tr>
                                                <th style="width: 5%">
                                                    {{ __('container.number') }}
                                                </th>
                                                <th>
                                                    {{ __('container.owner') }}
                                                </th>
                                                <th>
                                                    {{ __('container.using_for_client_and_us') }}
                                                </th>
                                                <th style="width: 30%">
                                                    {{ __('general.actions') }}
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($group->containers_list as $container)
                                                @if(!is_null($container))
                                                <tr>
                                                    <td>{{ $container->name }}</td>
                                                    <td>{{ $container->type == 'Аренда' ? optional($container->supplier)->name : __('container.own') }}</td>
                                                    <td>{{ __('container.grace_period') }}: {{ $container->grace_period_for_client }} {{ __('container.days') }} / {{ $container->grace_period_for_us }} {{ __('container.days') }}
                                                        <br>
                                                        <small>
                                                            {{ __('container.snp') }}: {{ $container->snp_amount_for_client }}{{ $container->snp_currency }} {{ __('container.in_day') }}
                                                            / {{ $container->snp_amount_for_us }}{{ $container->snp_currency }} {{ __('container.in_day') }}
                                                            <br>
                                                        </small>
                                                        {{ $container->additional_info != ''
                                                                ? __('general.additional_info').': '. $container->additional_info
                                                                : '' }}
                                                    </td>
                                                    <td>
                                                        <form class="inline-block"
                                                              action="{{ route('project.container_group.update', ["container_group" => $group->id, "project" => $project->id]) }}"
                                                              method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="action" value="delete_from_list">
                                                            <input type="hidden" name="container_id" value="{{ $container->id }}">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm delete-btn">
                                                                {{ __('container.remove_from_group') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <br>
                                        <form action="{{ route('project.container_group.destroy', ["container_group" => $group->id, "project" => $project->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="list_id" value="{{ $group->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm delete-btn">
                                                {{ __('container.remove_group') }} {{ $group->name }}
                                            </button>
                                        </form>
                                        <br><br>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            @else
                @include('error.dont_have_access')
            @endif
        </div>
    </section>
@endsection


