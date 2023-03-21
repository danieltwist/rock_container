<div class="btn-group dropup float-right">
    <button type="button" class="btn btn-secondary dropdown-toggle"
            data-toggle="modal"
            data-target="#add_file"
            aria-expanded="false"
            aria-controls="collapseExample">
        {{ __('project.project_comments') }} <span class="badge badge-dark right">{{ $comments->count() }}</span>
    </button>
</div>
