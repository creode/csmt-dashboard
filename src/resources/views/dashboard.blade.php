@extends('layouts.app')

@section('title', 'Dashboard')

@section('additional-nav')
    <li><a href="/project/add">Add a new project</a></li>
@endsection


@section('post-content')
    <ul id="projects-summary" class="tiles">

    </ul>
    <div id="projects-detailed"></div>
@endsection


@section('page-js')
    <script type="text/javascript">
        var items = [];

        @each('dashboard.item', $projects, 'project')

        items.forEach(addProject);







        function addProject(item) {
            var mini = createProjectMini(item);
            var detailed = createProjectDetailed(item);

            addMiniClickEvent(mini);
        }

        function createProjectMini(item) {
            var project = $('<li>')
                .data('projectid', item.id)
                .addClass('status-unknown')
                .html(item.name);

            $(project).appendTo('#projects-summary');

            return project;
        }

        function createProjectDetailed(item, mini) {
            var project = $('<div>')
                .addClass('project-details')
                .data('projectid', item.id);

            var overlay = $('<div>').addClass('overlay');
            $(overlay).appendTo(project);

            var closeButton = $('<a class="modal-close" href="#">').html('close').click(function() {
                $(project).hide();
                return false;
            }).appendTo(project);

            var testEnv = createProjectEnvironment(item, 'test');
            var liveEnv = createProjectEnvironment(item, 'live');
            $(testEnv).appendTo(project);
            $(liveEnv).appendTo(project);

            $(project).appendTo('#projects-detailed');
        }

        function createProjectEnvironment(item, environment) {
            var environment_url = environment + '_url';

            var wrapper = $('<div>')
                .addClass('environment')
                .data('environment', environment)
                .data('projectid', item.id)
                .data('url', item[environment_url]);

            var title = $('<h3>').html(environment);
            var subtitle = $('<h6>').html(item[environment_url]);
            var version = $('<div>').addClass('project-info project-version');
            var db = $('<div>').addClass('project-info project-db-snapshot-info');
            var media = $('<div>').addClass('project-info project-media-snapshot-info');

            switch(environment) {
                case 'live':
                    var takeDBBackup = $('<a class="project-action" href="/tool/database/snapshot/' + item.id + '/' + environment + '">Take DB backup</a>');
                    takeDBBackup.appendTo(db);
                    var takeMediaBackup = $('<a class="project-action" href="/tool/media/snapshot/' + item.id + '/' + environment + '">Take media backup</a>');
                    takeMediaBackup.appendTo(media);
                break;
                case 'test':
                    // <a href="#">Restore test DB backup</a>
                    // <a href="#">Restore test media backup</a>
                break;
            }
            var updateTool = $('<a class="project-action" href="/tool/update/' + item.id + '/' + environment + '">Update tool</a>');
            updateTool.appendTo(version);


            title.appendTo(wrapper);
            subtitle.appendTo(wrapper);
            version.appendTo(wrapper);
            db.appendTo(wrapper);
            media.appendTo(wrapper);

            return wrapper;
        }

        function addMiniClickEvent(mini) {
            $(mini).click(function() {
                var projectid = $(this).data('projectid');

                var detailed = getDetailsByProject(projectid);
                
                $(detailed).show();
            });
        }


        function getDetailsByProject(projectId) {
            return $('div.project-details', '#projects-detailed')
                .filter(function () {
                    return $(this).data("projectid") == projectId;
                });
        }

        function getMiniByProject(projectId) {
            return $('li', '#projects-summary')
                .filter(function () {
                    return $(this).data("projectid") == projectId;
                });
        }

        function toolRequest(projectId, toolUrl, element, loadingElement, callback) {
            var mini = getMiniByProject(projectId);

            $.ajax({
                url: toolUrl,
                beforeSend: function( xhr ) {
                    startLoading(loadingElement);
                    startLoading(mini);
                }
            }).done(function(data) {
                callback(data, element);
                doneLoading(loadingElement);
                doneLoading(mini);
                updateStatus(mini);
            });
        }

        
        function startLoading(loadingElement) {
            var loadCount = $(loadingElement).data('loadCount');
            if (typeof loadCount == 'undefined') {
                loadCount = 0;
            }

            loadCount++;

            $(loadingElement).data('loadCount', loadCount);

            if (loadCount == 1) {
                var loading = $('<div class="loading">');
                $(loading).appendTo(loadingElement);
            }
        }

        function doneLoading(loadingElement) {
            var loadCount = $(loadingElement).data('loadCount');

            loadCount--;

            $(loadingElement).data('loadCount', loadCount);

            if (loadCount == 0) {
                $('div.loading', loadingElement).remove();
            }
        }

        function populateVersion() {
            var element = this;
            var wrapper = $(element).parent();
            var id = $(wrapper).data('projectid');
            var env = $(wrapper).data('environment');

            var url = '/tool/version/' + id + '/' + env;

            toolRequest(
                id,
                url,
                element,
                element,
                function( data ) {
                    $('.current-version', element).remove();
                    var currentVersion = $("<div class='current-version'>").html(data);
                    $(currentVersion).appendTo(element);
                }
            );
        };

        function populateDbSnapshotInfo() {
            var element = this;
            var wrapper = $(element).parent();
            var id = $(wrapper).data('projectid');
            var env = $(wrapper).data('environment');

            var url = '/tool/database/info/' + id + '/' + env;

            toolRequest(
                id,
                url,
                element,
                element,
                populateSnapshotInfo
            );
        }

        function populateMediaSnapshotInfo() {
            var element = this;
            var wrapper = $(element).parent();
            var id = $(wrapper).data('projectid');
            var env = $(wrapper).data('environment');

            var url = '/tool/media/info/' + id + '/' + env;

            toolRequest(
                id,
                url,
                element,
                element,
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

            if (fileInfo.error) {
                $(element).html(fileInfo.message);
                return;
            }

            var snapshots = $('<ul class="snapshots tiles">');
            $(snapshots).appendTo(element);

            fileInfo.files.forEach(function(file) {
                var warnings = [];

                if (typeof file.date === 'undefined' || !file.date) {
                    var fileDateElement = $('<div class="project-file-date">').html('N/A');

                    warnings.push('Date not specified');
                } else {
                    var fileDate = new Date(file.date.date); 

                    var fileDateElement = $('<div class="project-file-date">').html(
                        fileDate.toLocaleDateString("en-GB") + ' @ ' + fileDate.toLocaleTimeString("en-GB")
                    );

                    // TODO: Use cron job to schedule backups and check against this date
                    // what this is actually doing now is just getting the last Monday at 00:00:00
                    var threshold = new Date();
                    threshold.setDate(threshold.getDate() - threshold.getDay() + 1);
                    threshold.setHours(0,0,0,0);

                    if (fileDate.getTime() < threshold.getTime()) {
                        warnings.push('Expired');
                    }
                }

                var fileSizeElement = $('<div class="project-file-size">').html(
                    fileSizeToString(file.size)
                );

                if (file.size < 0) {
                    warnings.push('File not found');
                } else if (file.size == 0) {
                    warnings.push('Empty File');
                }

                var item = $('<li class="project-file-info">').html(
                    '<h5>' + file.name + '</h5>'
                );

                $(fileSizeElement).appendTo(item);
                $(fileDateElement).appendTo(item);
                $(item).appendTo(snapshots);

                var notice = $('<div class="notice">');

                if (warnings.length > 0) {
                    $(item).addClass('status-warning');
                    notice.html(warnings.join(' and '));
                } else {
                    $(item).addClass('status-ok');
                    notice.html('Up to date');
                }

                $(notice).appendTo(item);
            });
        }

        function updateStatus(element) {
            var projectId = $(element).data('projectid');

            var detailed = getDetailsByProject(projectId);

            $(element).removeClass('status-warning')
                .removeClass('status-ok')
                .removeClass('status-unknown');

            if ($('.status-warning', detailed).length > 0) {
                $(element).addClass('status-warning');
            } else if ($('.status-ok', detailed).length > 0) {
                $(element).addClass('status-ok');
            } else {
                $(element).addClass('status-unknown');
            }


        }



        // number of projects to refresh at a time
        var segmentSize = 1;

        var totalProjects = $('#projects-detailed > div').length;
        var indexLow = 0;
        var indexHigh = segmentSize;

        function refreshDetails() {
            var projectsToRefresh = $('#projects-detailed > div')
                .filter(function(index) {
                    return (index + 1) <= indexHigh && (index + 1) > indexLow;
                });

            $('.project-version', projectsToRefresh).each(populateVersion);
            $('.project-db-snapshot-info', projectsToRefresh).each(populateDbSnapshotInfo);
            $('.project-media-snapshot-info', projectsToRefresh).each(populateMediaSnapshotInfo);

            if (indexHigh >= totalProjects) {
                indexLow = 0;
                indexHigh = segmentSize;
            } else {
                indexLow = indexHigh;
                indexHigh = indexHigh + segmentSize;
            }
        }

        $(document).ready(function() {
            refreshDetails();

            setInterval(function() {
                refreshDetails();
            }, 5000); // how often do we auto refresh?
        });        
    </script>
@stop
