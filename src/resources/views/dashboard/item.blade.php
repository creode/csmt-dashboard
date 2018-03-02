items.push({
    "id": "{{ $project->id }}",
    "name": "{{ $project->project_name }}",
    "live_url": "{{ $project->live_url }}",
    "test_url": "{{ $project->test_url }}"
});



/**
<table class="project-dashboard-table">
    <tr class="heading">
        <td colspan="3">
            <div class="title">
                {{ $project->project_name }}
            </div>
            <div class="buttons">
                <a class="showhide" href="#" onclick="$(this).parent().parent().parent().nextUntil( 'tr.heading' ).toggle(); $(this).parent().siblings('.summary').toggle(); $(this).parent().siblings('.title').toggle(); return false;">Min/Maximise</a>
                <a class="delete" href="/project/delete/{{ $project->id }}" onclick="return confirm('Are you sure you want to delete this project? This cannot be undone');">Delete Project</a>
            </div>
            <div class="summary" style="display: none">
                <table>
                    <tr>
                        <td style="width: 40%"><strong>{{ $project->project_name }}</strong></td>
                        <td style="width: 10%; text-align: right"><strong>Live Status:&nbsp;</strong></td>
                        <td class="status" style="width: 10%;">OK</td>
                        <td style="width: 10%; text-align: right"><strong>Test Status:&nbsp;</strong></td>
                        <td class="status-test" style="width: 10%;">OK</td>
                        <td style="width: 20%"></td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>

    @include('dashboard.environment', array('project' => $project, 'environment' => 'live', 'tool_url' => $project->live_url))
    @include('dashboard.environment', array('project' => $project, 'environment' => 'test', 'tool_url' => $project->test_url))
</table>
**/
