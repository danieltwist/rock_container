<div class="row">
    <div class="col-md-3 col-6">
        <div class="small-box bg-gradient-primary">
            <div class="inner">
                <h4>{{ $bought }} @if($bought != $bought_fact) / {{ $bought_fact }}@endif</h4>
                <p>Куплено</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-gradient-navy">
            <div class="inner">
                <h4>{{ $sold }} @if($sold != $sold_fact) / {{ $sold_fact }}@endif</h4>
                <p>Продано</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-gradient-indigo">
            <div class="inner">
                <h4>{{ $get }} @if($get != $get_fact) / {{ $get_fact }}@endif</h4>
                <p>Взято в аренду</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-gradient-success">
            <div class="inner">
                <h4>{{ $give }} @if($give != $give_fact) / {{ $give_fact }}@endif</h4>
                <p>Выдано в аренду</p>
            </div>
        </div>
    </div>
</div>
