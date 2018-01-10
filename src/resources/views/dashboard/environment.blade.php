<tr>
    <td colspan="2" class="subheading">{{ ucfirst($environment) }}</td>
    <td class="project-actions" rowspan="2">
        <ul>
        @if($environment == 'live')
            <li class="snapshot">
                <a href="/tool/database/snapshot/{{ $project->id }}/{{ $environment }}">Take DB backup</a>
            </li>
            <li class="snapshot">
                <a href="/tool/media/snapshot/{{ $project->id }}/{{ $environment }}">Take media backup</a>
            </li>
            <li class="update">
                <a href="/tool/update/{{ $project->id }}/{{ $environment }}">Update tool</a>
            </li>
        @elseif($environment == 'test')
            @if(isset($tool_url))
            <li>
                <a href="#">Restore test DB backup</a>
            </li>
            <li>
                <a href="#">Restore test media backup</a>
            </li>
            <li class="update">
                <a href="/tool/update/{{ $project->id }}/{{ $environment }}">Update tool</a>
            </li>
            @else
            <li>
                {{ ucfirst($environment) }} environment is not configured
            </li>
            @endif
        @endif
        </ul>
    </td>
</tr>
<tr>
    <td class="centre">
        {{ $tool_url or "no url" }}
    </td>
    <td class="project-info">
        @if(isset($tool_url))
        <div id="{{ $project->id }}_{{ $environment }}_version">
            <h4>Version</h4>
            <div class="project-version"
                data-projectid="{{ $project->id }}"
                data-environment="{{ $environment }}">
            </div>
        </div>
        <div id="{{ $project->id }}_{{ $environment }}_database_info">
            <h4>Database snapshot info</h4>
            <div class="project-db-snapshot-info"
                data-projectid="{{ $project->id }}"
                data-environment="{{ $environment }}">
            </div>  
        </div>
        <div id="{{ $project->id }}_{{ $environment }}_media_info">
            <h4>Media snapshot info</h4>
            <div class="project-media-snapshot-info"
                data-projectid="{{ $project->id }}"
                data-environment="{{ $environment }}">
            </div>  
        </div>
        @else
        <div>
            <h4>oh dear</h4>
            {{ ucfirst($environment) }} environment is not configured
        </div>
        @endif
    </td>
</tr>
