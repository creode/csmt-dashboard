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
    <div id="projects-refresh"><a href="#">&nbsp;</a></div>
@endsection


@section('page-js')
    <script type="text/javascript">
        var refreshInterval;

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
            var displayDate = lastBackupDate.getDate() + '/' + (lastBackupDate.getMonth() + 1) + '/' + lastBackupDate.getFullYear();
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

            var projectAnalytics = createProjectAnalytics(item);
            $(projectAnalytics).appendTo(project);

            var environmentsWrapper = $('<div>')
                .addClass('environments');

            var testEnv = createProjectEnvironment(item, 'test');
            var liveEnv = createProjectEnvironment(item, 'live');
            $(testEnv).appendTo(environmentsWrapper);
            $(liveEnv).appendTo(environmentsWrapper);

            $(environmentsWrapper).appendTo(project);

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

            var environmentInner = $('<div>').addClass('environment-inner');
            environmentInner.appendTo(wrapper);

            var environmentExpand = $('<a>').attr('href', '#').addClass('environment-expand');
            environmentExpand.appendTo(environmentInner);
            environmentExpand.click(expandEnvironment);

            var environmentTitle = $('<span>').html(environment);
            var title = $('<h4>').html(item[environment_url]);
            environmentTitle.prependTo(title);
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


            title.appendTo(environmentInner);
            version.appendTo(environmentInner);
            db.appendTo(environmentInner);
            media.appendTo(environmentInner);

            return wrapper;
        }

        function expandEnvironment() {
            var environment = $(this).closest('.environment');

            var isBeingExpanded = !environment.hasClass('environment-expanded');

            var siblingEnvironment = environment.siblings('.environment');
            
            if (isBeingExpanded) {
                environment.addClass('environment-expanded');
                siblingEnvironment.addClass('hidden');
            } else {
                environment.removeClass('environment-expanded');
                siblingEnvironment.removeClass('hidden');
            }
        }

        function createProjectAnalytics(item) {
            console.debug('Adding ' + item.name + ' analytics');

            var wrapper = $('<div>')
                .addClass('graphs')
                .data('projectid', item.id);

            // create wrapper elements and add to page
            var visitorGraphWrapperId = 'project-' + item.id + '-visitor-graph';
            var bounceRateGraphWrapperId = 'project-' + item.id + '-bounce-graph';
            var serverDowntimeGraphWrapperId = 'project-' + item.id + '-downtime-graph';

            var visitorGraphWrapper = $('<div>').addClass('stats-graph').attr('id', visitorGraphWrapperId).data('percentage', false);
            var bounceRateGraphWrapper = $('<div>').addClass('stats-graph').attr('id', bounceRateGraphWrapperId).data('percentage', true);
            var serverDowntimeGraphWrapper = $('<div>').addClass('stats-graph').attr('id', serverDowntimeGraphWrapperId).data('percentage', true);

            visitorGraphWrapper.appendTo(wrapper);
            bounceRateGraphWrapper.appendTo(wrapper);
            serverDowntimeGraphWrapper.appendTo(wrapper);

            // get data for project
            var visitorsData = [];
            var bounceRateData = [];
            var serverDowntimeData = [];

            // initialise the graphs
            // var visitorsGraph = initGraph(visitorsData, visitorGraphWrapper);
            // var bounceRateGraph = initGraph(bounceRateData, bounceRateGraphWrapper);
            // var serverDowntimeGraph = initGraph(serverDowntimeData, serverDowntimeGraphWrapper);


            // var title = $('<h4>').html(environment + ' : ' + item[environment_url]);
            // var version = $('<div>').addClass('project-info project-version');
            // var db = $('<div>').addClass('project-info').addClass('project-db-snapshot-info');
            // var media = $('<div>').addClass('project-info project-media-snapshot-info');

            // var actionsVersion = $('<ul>').addClass('project-actions');
            // var actionsDB = $('<ul>').addClass('project-actions');
            // var actionsMedia = $('<ul>').addClass('project-actions');

            // actionsVersion.appendTo(version);
            // actionsDB.appendTo(db);
            // actionsMedia.appendTo(media);

            return wrapper;
        }

        function initGraph(data, wrapperElement, colourIndex, isPercentage) {
            var fillColourOptions = [
                ['#5187E0', '#FFF'],
                ['#19FF00', '#FFF'],
                ['#ED07FF', '#FFF']
            ];

            var lineColourOptions = [
                ['#5997E6'],
                ['#0ABE20'],
                ['#B947C3']
            ];

            var fillColours = fillColourOptions[colourIndex];
            var lineColour = lineColourOptions[colourIndex];

            var options = {
                chart: {
                    height: 200,
                    type: 'area',
                    background: '#fff',
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                    colors: lineColour
                },
                series: [{
                    name: data.label,
                    data: data.yaxisValues
                }],
                title: {
                    text: data.title,
                    align: 'left',
                    offsetX: 20,
                    offsetY: 30,
                    floating: true
                },
                subtitle: {
                    text: data.headlineFigure,
                    align: 'left',
                    offsetX: 20,
                    offsetY: 80,
                    floating: true
                },
                xaxis: {
                    // type: 'datetime',
                    categories: data.xaxisValues,
                    labels: { show: false },
                    tooltip: { enabled: false },
                    floating: true,
                    axisTicks: {
                        show: false
                    },
                    axisBorder: {
                        show: false
                    },
                    labels: {
                        show: false
                    },
                },
                yaxis: {
                    show: false,
                    floating: true,
                    axisTicks: {
                        show: false
                    },
                    axisBorder: {
                        show: false
                    },
                    labels: {
                        show: false
                    },
                    // opposite: true
                },
                grid: {
                    xaxis: {
                        lines: {
                            show: false,
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false,
                        }
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    }
                },
                legend: {
                    // horizontalAlign: 'left'
                    show: false
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                      type: "horizontal",
                      colorStops: [
                          [
                            {
                              offset: 0,
                              color: fillColours[0],
                              opacity: 0.12
                            },
                            {
                              offset: 100,
                              color: fillColours[1],
                              opacity: 0.12
                            }
                          ]
                      ]
                    },
                }
            }

            if (isPercentage) {
                options.yaxis.min = 0;
                options.yaxis.max = 100;
            }

            var chart = new ApexCharts(
                wrapperElement,
                options
            );

            chart.render();
        }

        function addMiniClickEvent(mini) {
            $(mini).click(function() {
                var projectid = $(this).data('projectid');

                var detailed = getDetailsByProject(projectid);

                var projectTitle = $('span', this).html();
                $('.navbar-title').addClass('navbar-project-open').html('<a href="#"></a>' + projectTitle);
                $('a', '.navbar-title').click(closeProjectDetails);
                
                $(detailed).show();

                $('.graphs > div.stats-graph', detailed).each(function(item, el) {
                    var length = $(el).children('.apexcharts-canvas').length;
                    if (length == 0) {
                        var data = getGraphDataForProject(projectid);
                        var percentage = false;

                        if ($(el).data('percentage')) {
                            console.log('got a percentage data thing');
                            percentage = true;
                        }

                        initGraph(data[item], el, item, percentage);
                    }
                });
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
            var wrapper = $(element).closest('div.environment');
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
            var wrapper = $(element).closest('div.environment');
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
            var wrapper = $(element).closest('div.environment');
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


                var item = $('<li>').addClass('project-file-info');
                var itemTitle = $('<h5>').html(file.name.substr(file.name.lastIndexOf('/') + 1));
                var itemPath = $('<h6>').html(file.name.substr(0, file.name.lastIndexOf('/') + 1));

                $(itemTitle).appendTo(item);
                $(fileDateElement).appendTo(item);
                $(fileSizeElement).appendTo(item);
                $(itemPath).appendTo(item);
                $(item).appendTo(snapshots);

                var notice = $('<div class="notice">');

                if (warnings.length > 0) {
                    $(item).addClass('status-warning');
                    notice.html(warnings.join(' and '));
                } else {
                    $(item).addClass('status-ok');
                    notice.html('');
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

        function getGraphDates() {
            return getGraphDatesMonthly();
        }

        function getGraphDatesDaily() {
            var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            var daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            var numberOfDates = 30;

            var todaysDate = new Date();

            var dates = [];

            for(var i = numberOfDates - 1; i >= 0; i--) {
                // we'll remove i days from current date
                var newDate = new Date();
                newDate.setDate(todaysDate.getDate() - i);

                // now add date to array
                dates.push( daysOfWeek[newDate.getDay()] + ' ' + newDate.getDate() + ' ' + months[newDate.getMonth()] );
            }

            return dates;
        }

        function getGraphDatesMonthly() {
            var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            var daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            var numberOfDates = 22;

            var todaysDate = new Date();
            // set date to first of the month
            todaysDate.setDate(1);

            var dates = [];

            for(var i = numberOfDates - 1; i >= 0; i--) {
                // we'll remove i months from current date
                var newDate = new Date();
                // we always want the first of the month
                newDate.setDate(1);
                newDate.setMonth(newDate.getMonth() -1 -i);

                // now add date to array
                dates.push( months[newDate.getMonth()] + ' ' + newDate.getFullYear() );
            }

            return dates;
        }

        function getGraphDataForProject(projectid) {
            var calculateHeadlineFigures = true;
            var projectXaxisValues = getGraphDates();
            var visitorsYaxisValues = [];
            var cpuYaxisValues = [];
            var bouncerateYaxisValues = [];


            switch(projectid) {
                case '9': // Sipsmith
                    visitorsYaxisValues = [
                        50521,42411,60745,68483,68732,68329,57875,73395,83444,85851,95885,116409,51653,42843,51082,70190,65683,54069,51038,57048,57271,55591
                    ];

                    cpuYaxisValues = [
                        46.89,14.45,52.77,35.51,16.29,48.74,57.71,30.82,33.71,41.75,49.49,19.89,33.75,12.43,46.66,27.99,12.44,13.15,18.78,19.78,37.24,28.02
                    ];

                    bouncerateYaxisValues = [
                        40.81,44.30,34.49,40.47,44.91,53.65,50.62,48.61,49.62,44.37,46.86,38.72,47.60,42.62,51.83,41.35,53.72,44.64,35.50,49.35,47.93,39.53
                    ];
                    break;
                case '20': // Watford FC - Meriden
                    visitorsYaxisValues = [
                        5564,6471,6168,5233,5848,6016,5358,5461,5918,7839,7332,4677,5535,5130,5041,4503,4492,3652,3336,3384,4654,5710
                    ];

                    cpuYaxisValues = [
                        59.49,56.59,12.99,13.77,21.27,55.67,33.19,52.17,35.40,22.84,54.04,46.32,34.63,57.53,22.62,18.80,45.94,47.59,12.17,37.10,28.49,12.70
                    ];

                    bouncerateYaxisValues = [
                        72.00,68.40,67.84,61.36,72.00,63.84,65.04,64.88,64.40,70.48,67.20,62.24,61.20,65.04,60.80,62.40,71.44,59.52,63.44,56.96,65.92,58.88
                    ];
                    break;
                case '24': // Lakehouse - Aaron Services
                    visitorsYaxisValues = [
                        11128,12942,12336,10466,11696,12032,10716,10922,11836,15678,14664,9354,11070,10260,10082,9006,8984,7304,6672,6768,9308,11420
                    ];

                    cpuYaxisValues = [
                        15.49,21.01,35.52,57.44,22.68,17.70,20.41,49.98,51.24,23.41,44.58,28.59,25.32,45.43,39.96,16.75,36.79,14.97,20.14,17.52,43.26,20.01
                    ];

                    bouncerateYaxisValues = [
                        50.53,53.02,58.38,50.46,59.06,57.07,55.32,54.82,55.32,57.57,56.76,56.26,55.20,56.38,58.87,50.96,54.70,51.09,54.01,50.09,57.50,54.08
                    ];
                    break;
                case '26': // Lakehouse - H2o Nationwide
                    visitorsYaxisValues = [
                        22256,25884,24672,20932,23392,24064,21432,21844,23672,31356,29328,18708,22140,20520,20164,18012,17968,14608,13344,13536,18616,22840
                    ];

                    cpuYaxisValues = [
                        17.06,20.38,47.45,47.83,53.18,27.26,58.89,29.42,53.64,10.53,51.97,22.03,17.64,13.57,24.43,12.46,24.78,33.76,57.29,31.64,40.45,49.65
                    ];

                    bouncerateYaxisValues = [
                        43.98,34.14,47.52,44.82,36.72,46.92,45.12,41.70,35.04,43.20,30.42,32.64,44.70,37.44,43.14,36.42,35.52,40.32,45.84,33.30,43.92,30.00
                    ];
                    break;
                case '27': // Lakehouse - KT Heating
                    visitorsYaxisValues = [
                        44512,51768,49344,41864,46784,48128,42864,43688,47344,62712,58656,37416,44280,41040,40328,36024,35936,29216,26688,27072,37232,45680
                    ];

                    cpuYaxisValues = [
                        21.10,30.94,46.42,46.98,44.05,42.43,17.42,23.69,54.95,59.10,21.70,51.21,18.64,36.25,44.39,57.94,12.76,33.08,29.15,32.12,52.62,12.45
                    ];

                    bouncerateYaxisValues = [
                        52.89,58.00,57.07,56.07,52.02,55.51,52.52,55.70,54.08,52.83,54.89,49.84,58.00,52.77,51.21,52.52,52.83,58.56,58.69,51.40,54.57,53.64
                    ];
                    break;
                case '28': // Lakehouse - Precision Lifts
                    visitorsYaxisValues = [
                        89024,103536,98688,83728,93568,96256,85728,87376,94688,125424,117312,74832,88560,82080,80656,72048,71872,58432,53376,54144,74464,91360
                    ];

                    cpuYaxisValues = [
                        48.40,33.59,16.52,38.09,42.72,48.87,15.41,11.56,38.44,40.18,29.59,46.11,56.85,47.63,41.16,31.40,10.30,43.68,23.12,11.76,18.64,11.47
                    ];

                    bouncerateYaxisValues = [
                        36.60,30.89,35.00,24.75,32.82,32.87,31.31,25.41,31.17,29.75,33.34,30.13,32.64,32.45,25.46,35.71,23.62,36.65,25.83,36.08,30.46,37.50
                    ];
                    break;
                case '29': // Lakehouse - Providor
                    visitorsYaxisValues = [
                        27501,26396,28860,28074,109326,198214,110820,33956,34020,37450,44654,52664,54432,31730,24210,22564,27790,33766,29836,31678,36232,37066
                    ];

                    cpuYaxisValues = [
                        25.57,40.95,55.32,22.20,26.01,49.18,44.20,52.88,23.48,32.08,37.52,33.26,36.74,47.55,11.46,10.52,33.33,44.60,18.63,51.63,50.46,28.02
                    ];

                    bouncerateYaxisValues = [
                        33.67,26.73,35.80,25.83,25.27,32.12,27.72,36.70,24.37,36.79,29.80,31.31,24.84,27.44,35.52,29.05,27.96,36.04,25.79,35.04,34.81,34.71
                    ];
                    break;
                case '30': // Tom Daxon 
                    visitorsYaxisValues = [
                        1142,1381,1519,1202,1569,1780,1878,1141,1077,4105,1082,1148,1022,1043,1117,1635,1246
                    ];

                    cpuYaxisValues = [
                        16.25,46.70,29.00,34.55,45.93,31.82,49.73,19.86,54.04,37.03,55.21,21.26,32.15,53.99,32.17,28.39,44.35,28.71,57.13,43.27,52.29,27.51
                    ];

                    bouncerateYaxisValues = [
                        58.31,58.94,56.07,55.63,52.96,56.69,55.95,55.57,56.76,52.89,55.70,53.02,52.46,58.81,58.06,54.76,51.77,50.09,50.21,57.32,54.95,55.32
                    ];
                    break;
                case '31': // Watford FC - Trust
                    visitorsYaxisValues = [
                        38835,34314,37519,36497,142124,257678,144065,44144,44225,48684,58051,68462,70762,41248,31473,29333,36128,43896,38787,41181,47101,48186
                    ];

                    cpuYaxisValues = [
                        43.53,42.67,49.83,38.97,50.11,48.28,16.92,20.94,19.38,45.41,17.35,10.21,10.54,47.59,36.41,42.60,33.93,23.10,32.95,13.19,43.37,20.02
                    ];

                    bouncerateYaxisValues = [
                        34.71,33.53,25.13,29.99,27.54,36.84,34.76,33.34,30.89,31.93,31.22,26.68,27.63,25.74,30.04,36.93,29.33,25.27,30.46,25.60,29.66,29.42
                    ];
                    break;
                case '32': // Door Designer 
                    visitorsYaxisValues = [
                        15457,17616,18117,16593,13971,13868,14646,17111,17768,19536,15918,9764,18747,16490,15828,14764,14024,12936,13136,13165,14064,15479
                    ];

                    cpuYaxisValues = [
                        46.01,50.58,39.15,38.83,58.01,44.81,37.53,25.66,36.04,46.19,52.81,45.79,48.48,42.07,14.81,44.70,27.08,19.94,28.52,54.01,54.62,28.51
                    ];

                    bouncerateYaxisValues = [
                        28.24,36.32,31.97,24.75,36.70,35.00,27.68,32.78,32.59,33.77,32.07,34.10,25.08,25.46,35.28,26.02,26.02,27.82,23.62,23.90,35.80,33.53
                    ];
                    break;
                case '33': // Arthur Brett
                    visitorsYaxisValues = [
                        1962,1757,1801,1682,1731,2090,2527,2780,2200,2871,3257,3437,2088,1971,7512,1980,2101,1870,1909,2044,2992,2280
                    ];

                    cpuYaxisValues = [
                        53.25,51.56,24.58,33.89,36.35,38.14,32.96,24.74,30.77,29.49,22.62,29.49,47.26,28.95,17.93,29.51,50.01,25.96,21.38,24.73,9.75,26.41
                    ];

                    bouncerateYaxisValues = [
                        32.26,24.65,35.42,25.69,30.46,36.46,23.76,31.46,24.56,34.38,27.58,30.56,34.24,34.38,28.34,34.15,35.28,25.46,34.86,28.43,26.68,34.81
                    ];
                    break;
                case '34': // Advance HE
                    visitorsYaxisValues = [
                        13751,13198,14430,14037,54663,99107,55410,16978,17010,18725,22327,26332,27216,15865,12105,11282,13895,16883,14918,15839,18116,18533
                    ];

                    cpuYaxisValues = [
                        55.12,32.84,20.12,24.04,37.93,39.82,22.06,26.95,33.22,37.58,36.51,40.01,44.91,45.10,58.38,52.70,16.90,41.04,34.24,52.38,26.86,40.42
                    ];

                    bouncerateYaxisValues = [
                        55.26,49.84,56.26,51.02,54.33,55.95,54.01,58.06,54.64,51.21,58.00,54.82,53.45,56.44,55.76,53.76,49.90,50.71,53.76,52.39,51.83,53.02
                    ];
                    break;
                case '35': // CAP Business
                    visitorsYaxisValues = [
                        5661,5077,5691,5524,5642,5573,5611,5535,5403,5768,5653,5536,5882,5865,6130,5949,6168,5812,6127,6094,5948,5974
                    ];

                    cpuYaxisValues = [
                        12.21,55.39,17.66,23.05,26.09,2.67,58.58,39.82,55.51,32.77,14.67,42.01,31.02,33.08,25.31,30.54,36.76,50.11,32.93,15.37,13.97,31.33
                    ];

                    bouncerateYaxisValues = [
                        34.19,23.62,32.73,34.19,37.50,33.77,32.35,34.57,24.18,36.70,27.72,37.36,28.67,31.93,28.90,37.03,25.13,24.04,33.53,30.18,31.69,31.46
                    ];
                    break;
                case '37': // Pro:voke
                    visitorsYaxisValues = [
                        1886,21095,13257,4552,11528,18847,22104,9227,4661,5226,3216,9927,17067,3913,3579,2979,3263
                    ];

                    cpuYaxisValues = [
                        0.00,0.00,0.00,0.00,0.00,16.42,54.70,40.63,36.09,22.83,27.88,22.37,42.20,33.16,45.87,43.00,51.39,23.44,45.84,38.91,20.09,29.08
                    ];

                    bouncerateYaxisValues = [
                        0.00,0.00,0.00,0.00,0.00,52.89,52.27,55.32,56.51,51.40,54.76,52.77,51.58,54.51,56.26,55.95,51.52,50.46,50.96,57.38,51.46,54.08
                    ];
                    break;
                case '38': // Proxima
                    visitorsYaxisValues = [
                        6777,6513,7223,7944,7377,7786,7187,6658,7581,8259,9084,7670,9150,7514,6843,5158,5209,6174,5192,4228,4952,6774
                    ];

                    cpuYaxisValues = [
                        38.49,44.56,25.56,48.46,38.64,19.29,15.28,37.44,52.50,47.18,13.08,21.03,15.90,28.01,26.72,31.21,16.38,25.69,43.53,29.03,12.61,36.13
                    ];

                    bouncerateYaxisValues = [
                        56.51,53.39,52.96,51.90,55.45,51.71,56.19,57.32,54.08,56.26,54.95,52.02,58.13,51.02,55.76,56.32,54.70,54.57,57.25,50.34,54.64,55.51
                    ];
                    break;
                case '39': // Soft & Gentle
                    visitorsYaxisValues = [
                        38835,34314,37519,36497,142124,257678,144065,44144,44225,48684,58051,68462,70762,41248,31473,29333,36128,43896,38787,41181,47101,48186
                    ];

                    cpuYaxisValues = [
                        54.72,45.75,27.04,18.46,15.03,40.75,11.79,12.96,31.81,23.99,14.19,34.89,27.35,47.96,56.12,16.17,39.85,37.55,31.63,36.96,21.46,17.17
                    ];

                    bouncerateYaxisValues = [
                        31.50,26.26,24.13,27.25,28.48,25.27,29.19,35.66,34.48,36.84,35.89,30.42,26.92,27.06,29.47,36.37,30.23,35.42,32.97,35.99,37.17,31.60
                    ];
                    break;
                case '40': // Tranmere Park Primary
                    visitorsYaxisValues = [
                        0,0,0,0,0,0,167,187,156,201,222,209,278,302,305,345,367,356,295,298,306,312
                    ];

                    cpuYaxisValues = [
                        0.00,0.00,0.00,0.00,0.00,0.00,46.77,34.85,16.62,38.05,15.84,40.12,47.35,48.76,12.83,27.64,12.90,44.02,39.55,20.15,54.11,43.63
                    ];

                    bouncerateYaxisValues = [
                        0.00,0.00,0.00,0.00,0.00,0.00,25.65,37.50,33.67,34.95,37.17,29.05,24.84,29.24,26.59,37.26,25.65,32.73,36.04,32.78,27.72,27.68
                    ];
                    break;
                case '41': // TSG
                    visitorsYaxisValues = [
                        38816,34299,37507,36490,142115,257667,144062,44127,44221,48669,58047,68449,70762,41239,31467,29333,36127,43891,38775,41175,47101,48184
                    ];

                    cpuYaxisValues = [
                        56.60,53.33,53.00,25.62,17.16,54.67,33.86,59.82,12.15,53.38,25.16,10.53,49.66,36.14,58.96,45.72,24.01,53.00,34.42,45.56,53.34,23.91
                    ];

                    bouncerateYaxisValues = [
                        51.46,55.95,56.38,55.95,57.75,53.08,51.52,55.45,51.77,55.51,50.09,57.38,56.01,58.25,59.19,57.07,55.01,55.38,51.34,56.01,57.32,52.27
                    ];
                    break;
                case '42': // Twice Fired Glass
                    visitorsYaxisValues = [
                        110,141,136,112,121,193,124,120,110,149,116,148,113,154,154,132,198,180,154,136,130,129
                    ];

                    cpuYaxisValues = [
                        24.94,15.25,20.79,49.58,22.31,16.39,41.90,10.58,27.74,16.90,20.48,45.25,17.31,42.73,46.62,51.05,29.82,53.79,53.98,34.55,26.08,13.78
                    ];

                    bouncerateYaxisValues = [
                        58.50,52.21,58.62,53.39,56.51,55.38,54.45,55.45,51.83,51.90,55.07,53.20,52.58,54.51,55.32,49.90,54.76,52.21,52.33,53.33,55.57,56.76
                    ];
                    break;
                case '43': // Distinction Doors
                    visitorsYaxisValues = [
                        15457,17616,18117,16593,13971,13868,14646,17111,17768,19536,15918,9764,18747,16490,15828,14764,14024,12936,13136,13165,14064,15479
                    ];

                    cpuYaxisValues = [
                        28.55,22.76,12.98,36.71,48.29,43.08,30.93,33.22,11.87,40.35,30.49,27.42,25.87,27.76,33.70,58.69,32.11,43.20,26.41,34.14,31.68,24.96
                    ];

                    bouncerateYaxisValues = [
                        26.12,28.01,31.55,25.83,32.16,26.87,31.79,36.65,31.74,37.78,37.55,28.34,33.82,34.19,30.27,35.38,37.36,35.00,26.64,31.03,29.14,26.45
                    ];
                    break;



                default:
                    projectXaxisValues = [0];
                    visitorsYaxisValues = [0];
                    cpuYaxisValues = [0];
                    bouncerateYaxisValues = [0];
                    
                    calculateHeadlineFigures = false;
            }


            data =
            [
                { 
                    title: 'Total Visitors',
                    headlineFigure: '-',
                    yaxisValues: visitorsYaxisValues,
                    xaxisValues: projectXaxisValues,
                    label: 'Visitors'
                },
                { 
                    title: 'CPU Usage (% avg)',
                    headlineFigure: '-',
                    yaxisValues: cpuYaxisValues,
                    xaxisValues: projectXaxisValues,
                    label: 'Average CPU %'
                },
                { 
                    title: 'Bounce Rate (% avg)',
                    headlineFigure: '-',
                    yaxisValues: bouncerateYaxisValues,
                    xaxisValues: projectXaxisValues,
                    label: 'Bounce Rate %'
                }
            ];

            if (calculateHeadlineFigures) {
                // calculate the headline figure for total visitors
                data[0].headlineFigure = data[0].yaxisValues.reduce((a, b) => a + b).toLocaleString();
                // calculate the headline figure for average bounce rate
                data[1].headlineFigure = (data[1].yaxisValues.reduce((a,b) => a + b, 0) / data[1].yaxisValues.length).toFixed(2) + '%';
                // calculate the headline figure for average CPU usage
                data[2].headlineFigure = (data[2].yaxisValues.reduce((a,b) => a + b, 0) / data[1].yaxisValues.length).toFixed(2) + '%';
            }

            return data;
        }




        $(document).ready(function() {
            // refresh all projects when we load the page
            refreshDetails($('#projects-detailed > div'));

            // refresh teh next batch of projects when the button is clicked
            $('#projects-refresh').click(function() {
                clearInterval(refreshInterval);

                refreshNextSegmentDetails();

                refreshInterval = setInterval(function() {
                    refreshNextSegmentDetails();
                }, 30000); // how often do we auto refresh?                
            });

            refreshInterval = setInterval(function() {
                refreshNextSegmentDetails();
            }, 30000); // how often do we auto refresh?
        });
    </script>
@stop

