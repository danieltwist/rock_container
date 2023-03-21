<i class="fas fa-image bg-indigo"></i>
<div class="timeline-item">
    <h3 class="timeline-header">
        {{ __('container.project_arrival_photos') }} {{ $container_project->photos != '' ? 'загружены' : 'не загружены' }}
    </h3>
    @if($container_project->photos == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="upload_photos">
                <div class="form-group">
                    <input type="file" class="form-control-file" name="photos[]" multiple>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_div":{"div_id":"photos"}}'>
                    {{ __('general.upload') }}
                </button>
            </form>
        </div>
    @else
        <div class="timeline-body">
            <div id="photos_carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    @php
                        $i=0;
                    @endphp
                    @foreach($photos as $file)
                        @if ($i == 0)
                            <div class="carousel-item active">
                        @else
                            <div class="carousel-item">
                        @endif
                        <img class="d-block w-100" src="{{ Storage::url($file) }}">
                            </div>
                        @php $i++ @endphp
                    @endforeach
                        <a class="carousel-control-prev" href="#photos_carousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#photos_carousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="timeline-footer">
                <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="action" value="delete_photos">
                    <button type="submit" class="btn btn-outline-danger btn-sm"
                            data-action='{"update_div":{"div_id":"photos"}}'>
                        {{ __('general.remove') }}
                    </button>
                </form>
            </div>
            @endif
</div>
