@extends('layouts.app')

@section('title', 'Dashboard')

@section('additional-nav')
    <li><a href="/project/add">Add a new project</a></li>
@endsection


@section('post-content')
    <div class="container">
        <h3 class="section-title">OVERVIEW</h3>
        <ul id="status-summary" class="tiles stats">
            <li class="error" id="stats-errors" data-count="0">
                <div>
                    <span>Errors</span>
                    <h3>0</h3>
                </div>
            </li>
            <li class="warning" id="stats-warnings" data-count="0">
                <div>
                    <span>Warnings</span>
                    <h3>0</h3>
                </div>
            </li>
            <li class="clock" id="stats-last-backup-date">
                <div>
                    <span>Last Scheduled Backup</span>
                    <h3>...</h3>
                </div>
            </li>
            <li class="database" id="stats-total-backups" data-count="0">
                <div>
                    <span>Total Backups (weekly)</span>
                    <h3>0</h3>
                </div>
            </li>
        </ul>

        <h3 class="section-title">SITES</h3>
        <ul id="projects-summary" class="tiles">

        </ul>
    </div>
    <div id="projects-detailed"></div>
@endsection


@section('page-js')
    <script type="text/javascript">
        var items = [];

        @each('dashboard.item', $projects, 'project')

        items.forEach(addProject);

        toastr.options.progressBar = true;

        addActionClicks();

        updateLastBackupDate();








        function increaseWarningCount() {
            var newWarningCount = $('#stats-warnings').data('count') + 1;
            $('#stats-warnings').data('count', newWarningCount);
            $('#stats-warnings h3').html(newWarningCount);
        }

        function decreaseWarningCount() {
            var newWarningCount = $('#stats-warnings').data('count') - 1;
            $('#stats-warnings').data('count', newWarningCount);
            $('#stats-warnings h3').html(newWarningCount);
        }

        function updateLastBackupDate() {
            var lastBackupDate = new Date();
            lastBackupDate.setDate(lastBackupDate.getDate() - lastBackupDate.getDay() + 1);
            lastBackupDate.setHours(0,0,0,0);
            var displayDate = lastBackupDate.getDate() + '/' + lastBackupDate.getMonth() + '/' + lastBackupDate.getFullYear();
            $('#stats-last-backup-date h3').html(displayDate);
        }

        function addProject(item) {
            var mini = createProjectMini(item);
            var detailed = createProjectDetailed(item);

            addMiniClickEvent(mini);
        }

        function createProjectMini(item) {
            console.debug('Creating summary item for ' + item.name);

            var project = $('<li>')
                .data('projectid', item.id)
                .data('projectname', item.name)
                .addClass('status-unknown');

            var span = $('<span>')
                .html(item.name);

            $(span).appendTo(project);
            $(project).appendTo('#projects-summary');

            return project;
        }

        function createProjectDetailed(item, mini) {
            console.debug('Creating detailed item for ' + item.name);

            var project = $('<div>')
                .addClass('project-details')
                .data('projectid', item.id)
                .data('projectname', item.name);

            var overlay = $('<div>').addClass('overlay');
            $(overlay).appendTo(project);

            var sectionTitle = $('<h3>').addClass('section-title').html('ANALYTICS');
            $(sectionTitle).appendTo(project);            

            var testEnv = createProjectEnvironment(item, 'test');
            var liveEnv = createProjectEnvironment(item, 'live');
            $(testEnv).appendTo(project);
            $(liveEnv).appendTo(project);

            $(project).appendTo('#projects-detailed');
        }

        function closeProjectDetails() {
            $('#projects-detailed .project-details:visible').hide();
            $('.navbar-title').removeClass('navbar-project-open').html('<strong>Backup</strong>Dashboard');
        }

        function createProjectEnvironment(item, environment) {
            var environment_url = environment + '_url';

            if (!item[environment_url]) {
                console.debug('No ' + environment + ' url found for project ' + item.id);
                return;
            }

            console.debug('Adding ' + environment + ' environment with URL ' + item[environment_url]);

            var wrapper = $('<div>')
                .addClass('environment')
                .data('environment', environment)
                .data('projectid', item.id)
                .data('url', item[environment_url]);

            var title = $('<h4>').html(environment + ' : ' + item[environment_url]);
            var version = $('<div>').addClass('project-info project-version');
            var db = $('<div>').addClass('project-info').addClass('project-db-snapshot-info');
            var media = $('<div>').addClass('project-info project-media-snapshot-info');

            var actionsVersion = $('<ul>').addClass('project-actions');
            var actionsDB = $('<ul>').addClass('project-actions');
            var actionsMedia = $('<ul>').addClass('project-actions');

            actionsVersion.appendTo(version);
            actionsDB.appendTo(db);
            actionsMedia.appendTo(media);

            switch(environment) {
                case 'live':
                    console.debug('Creating button to take DB backup');
                    var takeDBBackup = $('<li><a class="project-action" href="/tool/database/snapshot/' + item.id + '/' + environment + '"><i class="fas fa-camera"></i></a></li>');
                    takeDBBackup.appendTo(actionsDB);
                    console.debug('Creating button to download DB backup');
                    var downloadDBBackup = $('<li><a class="project-action" href="/tool/database/download/' + item.id + '/' + environment + '"><i class="fas fa-cloud-download-alt"></i></a></li>');
                    downloadDBBackup.appendTo(actionsDB);
                    console.debug('Creating button to take media backup');
                    var takeMediaBackup = $('<li><a class="project-action" href="/tool/media/snapshot/' + item.id + '/' + environment + '"><i class="fas fa-camera"></i></a></li>');
                    takeMediaBackup.appendTo(actionsMedia);
                    console.debug('Creating button to download media backups');
                    var downloadMediaBackup = $('<li><a class="project-action" href="/tool/media/download/' + item.id + '/' + environment + '"><i class="fas fa-cloud-download-alt"></i></a></li>');
                    downloadMediaBackup.appendTo(actionsMedia);
                break;
                case 'test':
                    console.debug('Creating button to pull DB backup');
                    var pullDBBackup = $('<li><a class="project-action" href="/tool/database/pull/' + item.id + '"><i class="fas fa-sync-alt"></i></a></li>');
                    pullDBBackup.appendTo(actionsDB);
                    console.debug('Creating button to restore DB backup');
                    var restoreDBBackup = $('<li><a class="project-action" href="/tool/database/restore/' + item.id + '"><i class="fas fa-sign-in-alt"></i></a></li>');
                    restoreDBBackup.appendTo(actionsDB);
                    console.debug('Creating button to download DB backup');
                    var downloadDBBackup = $('<li><a class="project-action" href="/tool/database/download/' + item.id + '/' + environment + '"><i class="fas fa-cloud-download-alt"></i></a></li>');
                    downloadDBBackup.appendTo(actionsDB);

                    console.debug('Creating button to pull media backup');
                    var pullMediaBackup = $('<li><a class="project-action" href="/tool/media/pull/' + item.id + '"><i class="fas fa-sync-alt"></i></a></li>');
                    pullMediaBackup.appendTo(actionsMedia);
                    console.debug('Creating button to restore media backup');
                    var restoreMediaBackup = $('<li><a class="project-action" href="/tool/media/restore/' + item.id + '"><i class="fas fa-sign-in-alt"></i></a></li>');
                    restoreMediaBackup.appendTo(actionsMedia);
                    console.debug('Creating button to download media backups');
                    var downloadMediaBackup = $('<li><a class="project-action" href="/tool/media/download/' + item.id + '/' + environment + '"><i class="fas fa-cloud-download-alt"></i></a></li>');
                    downloadMediaBackup.appendTo(actionsMedia);
                break;
            }
            console.debug('Creating button to update tool');
            var updateTool = $('<li><a class="project-action" href="/tool/update/' + item.id + '/' + environment + '"><i class="fas fa-wrench"></i></a></li>');
            updateTool.appendTo(actionsVersion);


            title.appendTo(wrapper);
            version.appendTo(wrapper);
            db.appendTo(wrapper);
            media.appendTo(wrapper);

            return wrapper;
        }

        function addMiniClickEvent(mini) {
            $(mini).click(function() {
                var projectid = $(this).data('projectid');

                var detailed = getDetailsByProject(projectid);

                var projectTitle = $('span', this).html();
                $('.navbar-title').addClass('navbar-project-open').html('<a href=#"></a>' + projectTitle);
                $('a', '.navbar-title').click(closeProjectDetails);
                
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
            }).always(function(data) {
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
            var theUrl = $(element).siblings('h4').html();

            var previousTotal = $('ul.snapshots > li', element).length;

            $('ul.snapshots', element).remove();

            try {
                var fileInfo = JSON.parse(data);
            } catch (e) {
                $('div.snapshot-error', element).remove();
                $('<div>').addClass('snapshot-error').html(data).appendTo(element);
                toastr.error('Error populating snapshot info, see console for log data');
                console.log(data);
                return;
            }

            if (fileInfo.error) {
                $(element).html(fileInfo.message);
                toastr.error('Error when populating snapshot info');
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
                    '<h6>' + file.name.substr(0, file.name.lastIndexOf('/') + 1) + '</h6>' +
                    '<h5>' + file.name.substr(file.name.lastIndexOf('/') + 1) + '</h5>'
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

            var newTotal = $('ul.snapshots > li', element).length;
            var adjustment = newTotal - previousTotal;
            
            var currentCount = $('#stats-total-backups').data('count');

            var newTotalBackups = currentCount+adjustment;
            $('#stats-total-backups').data('count', newTotalBackups);
            $('#stats-total-backups h3').html(newTotalBackups);
        }

        function updateStatus(element) {
            var projectId = $(element).data('projectid');

            var detailed = getDetailsByProject(projectId);

            var projectName = $(detailed).data('projectname');

            if ($('.status-warning', detailed).length > 0) {
                if (!$(element).hasClass('status-warning')) {
                    $(element).addClass('status-warning');

                    toastr.error('transitioned to "warning"', projectName);

                    increaseWarningCount();
                }

                $(element).removeClass('status-ok').removeClass('status-unknown');
            } else if ($('.status-ok', detailed).length > 0) {
                if (!$(element).hasClass('status-ok')) {
                    $(element).addClass('status-ok');

                    toastr.success('transitioned to "ok"', projectName);
                }

                if ($(element).hasClass('status-warning')) { decreaseWarningCount(); }

                $(element).removeClass('status-warning').removeClass('status-unknown');
            } else {
                if (!$(element).hasClass('status-unknown')) {
                    $(element).addClass('status-unknown');

                    toastr.info('transitioned to "unknown"', projectName);
                }

                if ($(element).hasClass('status-warning')) { decreaseWarningCount(); }

                $(element).removeClass('status-warning').removeClass('status-ok');
            }
        }

        function addActionClicks() {
            $('a.project-action').click(function() {
                var projectInfoDiv = $(this).closest('div.project-info');
                var projectDetailsDiv = $(this).closest('div.project-details');
                var projectName = $(projectDetailsDiv).data('projectname');

                toolRequest(
                    $(projectDetailsDiv).data('projectid'),
                    this.href,
                    this,
                    projectInfoDiv,
                    function(data, element) {
                        console.log(data);
                        var oData = $.parseJSON(data);
                        if (oData.success) {

                            if (oData.links) {
                                var linkHTML = '';

                                Object.keys(oData.links).forEach(function(key){
                                    linkHTML = linkHTML + '<strong>' + key + '</strong> : <a href="' + oData.links[key] + '">download</a><br/>';
                                });

                                toastr.success(
                                    linkHTML,
                                    projectName,
                                    {
                                      "closeButton": true,
                                      "debug": false,
                                      "newestOnTop": false,
                                      "progressBar": false,
                                      "preventDuplicates": false,
                                      "onclick": null,
                                      "showDuration": "300",
                                      "hideDuration": "1000",
                                      "timeOut": 0,
                                      "extendedTimeOut": 0,
                                      "showEasing": "swing",
                                      "hideEasing": "linear",
                                      "showMethod": "fadeIn",
                                      "hideMethod": "fadeOut",
                                      "tapToDismiss": false
                                    }
                                );
                            } else {
                                toastr.success(oData.message, projectName);
                            }

                            refreshDetails($(projectDetailsDiv));
                        } else if (oData.info) {
                            toastr.info(oData.message, projectName);
                        } else {
                            toastr.error(oData.message, projectName);
                        }
                    }
                );

                return false;
            });
        }



        // number of projects to refresh at a time
        var segmentSize = 5;

        var totalProjects = $('#projects-detailed > div').length;
        var indexLow = 0;
        var indexHigh = segmentSize;

        function refreshNextSegmentDetails() {
            var projectsToRefresh = $('#projects-detailed > div')
                .filter(function(index) {
                    return (index + 1) <= indexHigh && (index + 1) > indexLow;
                });

            refreshDetails(projectsToRefresh);

            if (indexHigh >= totalProjects) {
                indexLow = 0;
                indexHigh = segmentSize;
            } else {
                indexLow = indexHigh;
                indexHigh = indexHigh + segmentSize;
            }
        }


        function refreshDetails(projectsToRefresh) { 
            $('.project-version', projectsToRefresh).each(populateVersion);
            $('.project-db-snapshot-info', projectsToRefresh).each(populateDbSnapshotInfo);
            $('.project-media-snapshot-info', projectsToRefresh).each(populateMediaSnapshotInfo);
        }

        $(document).ready(function() {
            // refresh all projects when we load the page
            refreshDetails($('#projects-detailed > div'));

            setInterval(function() {
                refreshNextSegmentDetails();
            }, 30000); // how often do we auto refresh?
        });
    </script>
@stop

