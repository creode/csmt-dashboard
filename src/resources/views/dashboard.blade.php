@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-16">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Project Dashboard
                    <a href="/project/add">Add a new project</a>
                </div>

                <div class="panel-body" style="padding: 0">
                    @each('dashboard.row', $projects, 'project')
                </div>
            </div>
        </div>
    </div>
@endsection


@section('page-js')
    <script type="text/javascript">
        function refreshDetails() {
            $('.project-version').each(populateVersion);
            $('.project-db-snapshot-info').each(populateDbSnapshotInfo);
            $('.project-media-snapshot-info').each(populateMediaSnapshotInfo);
        }

        $(document).ready(function() {
            refreshDetails();

            setInterval(function() {
                refreshDetails();
            }, 60000); // how often do we auto refresh?
        });
    </script>

    <script>
        function toolRequest(toolUrl, element, loadingElement, callback) {
            var loading = $('<div class="loading">');

            $.ajax({
                url: toolUrl,
                beforeSend: function( xhr ) {
                    $(loading).appendTo(loadingElement);
                }
            }).done(function(data) {
                callback(data, element);
                $(loading).remove();
                updateStatus(element);
            });
        }

        function populateVersion() {
            var element = this;
            var id = $(element).data('projectid');
            var env = $(element).data('environment');

            var url = '/tool/version/' + id + '/' + env;

            toolRequest(
                url,
                element,
                $(element).parent(),
                function( data ) {
                    $(element).html(data);
                }
            );
        };

        function populateDbSnapshotInfo() {
            var element = this;
            var id = $(element).data('projectid');
            var env = $(element).data('environment');

            var url = '/tool/database/info/' + id + '/' + env;

            toolRequest(
                url,
                element,
                $(element).parent(),
                populateSnapshotInfo
            );
        }


        function populateMediaSnapshotInfo() {
            var element = this;
            var id = $(element).data('projectid');
            var env = $(element).data('environment');

            var url = '/tool/media/info/' + id + '/' + env;

            toolRequest(
                url,
                element,
                $(element).parent(),
                populateSnapshotInfo
            );
        }

        function populateSnapshotInfo(data, element) {
            $('ul.snapshots', element).remove();

            try {
                var fileInfo = JSON.parse(data);
            } catch (e) {
                $(element).html(data);
                return;
            }

            var snapshots = $('<ul class="snapshots tiles">');
            $(snapshots).appendTo(element);

            fileInfo.files.forEach(function(file) {
                var fileDate = new Date(file.date);  

                var fileDateElement = $('<div class="project-file-date">').html(
                    fileDate.toLocaleDateString("en-GB") + ' @ ' + fileDate.toLocaleTimeString("en-GB")
                );

                var fileSizeElement = $('<div class="project-file-size">').html(
                    fileSizeToString(file.size)
                );

                var item = $('<li class="project-file-info">').html(
                    '<h5>' + file.name + '</h5>'
                );

                $(fileSizeElement).appendTo(item);
                $(fileDateElement).appendTo(item);
                $(item).appendTo(snapshots);


                // TODO: Use cron job to schedule backups and check against this date
                // what this is actually doing now is just getting the last Monday at 00:00:00
                var threshold = new Date();
                threshold.setDate(threshold.getDate() - threshold.getDay() + 1);
                threshold.setHours(0,0,0,0);

                var notice = $('<div class="notice">')

                if (fileDate.getTime() < threshold.getTime()) {
                    $(item).addClass('status-warning');
                    notice.html('Expired');
                } else {
                    $(item).addClass('status-ok');
                    notice.html('Up to date');
                }

                $(notice).appendTo(item);
            });
        }

        function updateStatus(element) {
            var table = $(element).closest('.project-dashboard-table');
            var summary = $('.summary', table);

            var warnings = $('.status-warning', element);

            // TODO: This doesn't know what the environment is, so can't update the correct status.
            // TODO: This doesn't allow for multiple requests per environment (which we already have!)
            // so it will only show the status of the latest call that was made
            if (warnings.length > 0) {
                $('.status', summary).html('WARNING');
                $(table).prependTo($(table).parent());
            } else {
                $('.status', summary).html('OK');
            }
        }
    </script>
@stop
