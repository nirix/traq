<div class="milestone-progress-bar">
    <div class="progress">
        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $milestone->getClosedPercent() }}%" aria-valuenow="{{ $milestone->getClosedPercent() }}" aria-valuemin="0" aria-valuemax="100"></div>
        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $milestone->getStartedPercent() }}%" aria-valuenow="{{ $milestone->getStartedPercent() }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                {{ __('milestones.x_closed', ['count' => $milestone->getClosedCount()]) }}
            </div>
            <div class="col-md-2">
                {{ __('milestones.x_started', ['count' => $milestone->getStartedCount()]) }}
            </div>
            <div class="col-md-2">
                {{ __('milestones.x_total', ['count' => $milestone->getTotalCount()]) }}
            </div>
        </div>
    </div>
</div>
