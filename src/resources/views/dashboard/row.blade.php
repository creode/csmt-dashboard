<tr>
    <td>
        {{ $project->project_name }}
    </td>
    <td>
        {{ $project->live_url }}
    </td>
    <td>
        <div id="{{ $project->id }}_live_version" style="display:none;">
            <label>Version</label>
            <span></span>
            <script>
                jQuery.ajax({
                    url: '/project/version/{{ $project->id }}/live'
                }).done(function( data ) {
                    if ( console && console.log ) {
                        $('#{{ $project->id }}_live_version span').html(data);
                        $('#{{ $project->id }}_live_version').show();
                    }
                });
            </script>    
        </div>
        <div id="{{ $project->id }}_live_database_info" style="display:none;">
            <label>Database snapshot info</label>
            <span></span>
            <script>
                jQuery.ajax({
                    url: '/project/database/info/{{ $project->id }}/live'
                }).done(function( data ) {
                    if ( console && console.log ) {
                        $('#{{ $project->id }}_live_database_info span').html(data);
                        $('#{{ $project->id }}_live_database_info').show();
                    }
                });
            </script>    
        </div>
    </td>
    <td>
        {{ $project->test_url }}
    </td>
    <td id="{{ $project->id }}_test_version">
        loading, please wait ...
        <script>
            jQuery.ajax({
                url: '/project/version/{{ $project->id }}/test'
            }).done(function( data ) {
                if ( console && console.log ) {
                    $('#{{ $project->id }}_test_version').html(data);
                }
            });
        </script>
    </td>
    <td>
        <ul>
            <li>
                <a href="/project/database/snapshot/{{ $project->id }}/live">Take live DB backup</a>
            </li>
            <li>
                <a href="#">Take live media backup</a>
            </li>
            <li>
                <a href="#">Restore test DB backup</a>
            </li>
            <li>
                <a href="#">Restore test media backup</a>
            </li>
            <li>
                <a href="#">Update tool</a>
            </li>
        </ul>
    </td>
</tr>
