@forelse($files as $key=>$value)
    <div class="col-md-4 col-sm-6 col-12">
        <div class="card card-outline card-warning collapsed-card">
            <div class="card-header cursor-pointer" data-card-widget="collapse">
                <h3 class="card-title">{{ $key }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool"
                            data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: none;">
                <ul class="nav flex-column">
                    @foreach($value as $key=>$folders)
                        <div class="mt-2">
                            @if($folders['folder'] != '')
                                <i class="fas fa-folder-open"></i> {{ $folders['folder'] }}
                            @endif
                            @foreach($folders['files'] as $file)
                                <li class="nav-item">
                                    <a class="nav-link"
                                       href="{{ Storage::url($file) }}"
                                       download>
                                        <i class="far fa-file-word"></i></i> {{ basename($file) }}
                                    </a>
                                </li>
                            @endforeach
                        </div>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@empty
    <div class="col-md-12">
        Файлы еще не были загружены
    </div>
@endforelse
