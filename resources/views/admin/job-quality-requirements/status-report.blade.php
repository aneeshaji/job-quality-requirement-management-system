<x-app-layout>
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            
            <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Job Quality Requirements /</span> List</h4> -->
            <!-- Hoverable Table rows -->
            <div class="card">
                <div id="sticky-wrapper" class="sticky-wrapper" style="height: 85.675px;">
                    <div class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row"
                        style="">
                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Job Quality Requirements /</span> Docs Status Report
                        </h4>
                        <div class="action-btns add-jqr-btn">
                            <!-- <button class="btn btn-label-primary me-3 waves-effect">
                                <span class="align-middle"> Back</span>
                            </button> -->
                            <!-- <a href="{{ url('/admin/job-quality-requirements/create') }}"
                                class="btn btn-primary waves-effect waves-light">Add JQR</a> -->
                        </div>
                    </div>
                </div>
                <!-- <h5 class="card-header">Hoverable rows</h5> -->
                <div class="card">
                    <!-- <h5 class="card-header">Advanced Search</h5> -->
                    <!--Search Form -->
                    <div class="card-body">
                        <form class="dt_adv_search">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-12 col-lg-6">
                                            <label class="form-label">DD Due Date (Start)</label>
                                            <input type="date" id="start_date" class="form-control" name="start_date"/>
                                        </div>
                                        <div class="col-12 col-sm-12 col-lg-6">
                                            <label class="form-label">DD Due Date (End)</label>
                                            <input type="date" id="end_date" class="form-control" name="end_date"/>
                                        </div>
                                        <div class="col-12 col-sm-12 col-lg-12 text-center">
                                            <button type="button" id="reset" onclick="generateReport()" class="btn rounded-pill btn-primary waves-effect waves-light">Generate</button>
                                        </div>
                                        <!-- <div class="col-12 col-sm-6 col-lg-4">
                                            <<button id="reset" class="btn btn-secondary waves-effect waves-light btn-reset" tabindex="0"
                                                aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog"
                                                aria-expanded="false"><span><i class="ti ti-reload"></i>
                                                    </span></button>
                                            <button type="button" id="reset" class="btn rounded-pill btn-info waves-effect waves-light">Info</button>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- <hr class="mt-0"> -->
                </div>
                <div class="table-responsive text-nowrap display-none" id="statustable">
                    <table class="table table-hover status-report-data-table">
                        <thead>
                            <tr>
                                <th>Job Number</th>
                                <th>DD Due Date</th>
                                <th>Traveller Completed Status(Piping)</th>
                                <th>Traveller Completed Status(Pressure Vessels)</th>
                            </tr>
                        </thead>
                    </table>
                    
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-12 text-center pt-10 display-none" id="actnBtns">
                <a id="downloadReport">
                    <button type="button" id="reportBtn" class="btn rounded-pill btn-success waves-effect waves-light">Download Report</button>
                </a>
                <a id="viewReport">
                    <button type="button" id="viewBtn" class="btn rounded-pill btn-warning waves-effect waves-light">View Report</button>
                </a>    
            </div>
        </div>
        <!-- / Content -->
        <hr class="container-m-nx mb-5" />
        <!-- Footer -->
        <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl">
                <div
                    class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                    <div>
                        ©
                        <script>
                        document.write(new Date().getFullYear());
                        </script>
                        All Rights Reserved
                        <!-- <a href="https://pixinvent.com" target="_blank"
                            class="footer-link text-primary fw-medium">codeFirstIT</a> -->
                    </div>
                </div>
            </div>
        </footer>
        <!-- / Footer -->

        <div class="content-backdrop fade"></div>
    </div>
</x-app-layout>
<script>
    var startDate = '';
    var endDate = '';
    function generateReport() {
        startDate = $('#start_date').val();
        endDate = $('#end_date').val();
        if (!startDate || !endDate) {
            Swal.fire({
                icon: "error",
                title: "Missing Dates",
                text: "Please select both start and end dates before proceeding."
            });
            return false;
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            var table = $('.status-report-data-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                searching: false,
                ajax: {
                    method: 'GET',
                    url: "{{ url('admin/get-doc-status-report') }}",
                    data: function(d) {
                        d.start_date = startDate,
                        d.end_date = endDate
                    },
                    dataSrc: function(response) {
                        // Check if the response has data or not
                        if (response.data.length) {
                            $('#statustable').show();
                            $('#actnBtns').show();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "No data found!",
                                text: "No data found for the selected date range!",
                            });
                            $('#statustable').hide();
                            $('#actnBtns').hide();
                        }
                        startDate = response.start_date;
                        endDate = response.end_date;
                        return response.data; // Ensure data is returned to DataTable
                    }
                },
                columns: [
                    {
                        data: 'job_number',
                        name: 'job_number'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'traveller_status_piping',
                        name: 'traveller_status_piping',
                        render: function(data, type, row) {
                            var statusClass = '';
                            if (data == 'Completed') {
                                statusClass = 'badge bg-label-success me-1';
                            } else if (data == 'Pending') {
                                statusClass = 'badge bg-label-warning me-1';
                            }
                            return '<span class="' + statusClass + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'traveller_status_pv',
                        name: 'traveller_status_pv',
                        render: function(data, type, row) {
                            var statusClass = '';
                            if (data == 'Completed') {
                                statusClass = 'badge bg-label-success me-1';
                            } else if (data == 'Pending') {
                                statusClass = 'badge bg-label-warning me-1';
                            }
                            return '<span class="' + statusClass + '">' + data + '</span>';
                        }
                    }
                ]
            });
        }
    }
    // Handle the click event on "View Report" button
    $('#viewReport').on('click', function(event) {
        event.preventDefault();  // Prevent default link behavior

        // Construct the URL with start_date and end_date parameters
        var reportUrl = "{{ url('/admin/view-doc-status-report') }}";
        var fullUrl = reportUrl + '?start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);

        // Open the report URL in a new tab with the appended parameters
        window.open(fullUrl, '_blank');
    });
    // Handle the click event on "View Report" button
    $('#downloadReport').on('click', function(event) {
        event.preventDefault();  // Prevent default link behavior

        // Construct the URL with start_date and end_date parameters
        var reportUrl = "{{ url('/admin/download-doc-status-report') }}";
        var fullUrl = reportUrl + '?start_date=' + encodeURIComponent(startDate) + '&end_date=' + encodeURIComponent(endDate);

        // Open the report URL in a new tab with the appended parameters
        window.open(fullUrl);
    });
</script>
