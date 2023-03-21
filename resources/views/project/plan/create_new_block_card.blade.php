<div class="card">
    <form action="{{ route('block_items.store') }}" method="POST">
        <div class="card-header">
            <h3 class="card-title">{{ __('project.create_block') }}</h3>
            @csrf
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="freight_amount">{{ __('project.block_name') }}</label>
                <input type="text" class="form-control" name="name"
                       placeholder="{{ __('project.block_name') }}">
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary float-right">{{ __('project.create_block') }}</button>
        </div>
    </form>
</div>
