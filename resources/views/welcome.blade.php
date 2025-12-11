<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learner Progress Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background:#111;
            color:#eee;
            font-family: Arial;
        }
        .card {
            background:#1b1b1b;
            border:1px solid #333;
            border-radius:10px;
            color:#fff;
        }
        .course-pill {
            padding:6px 10px;
            margin:3px;
            border-radius:18px;
            display:inline-flex;
            align-items:center;
            gap:6px;
            font-size:13px;
            font-weight:500;
            background:#222;
            border:1px solid #333;
            color:#fff;
        }
        .progress-circle {
            width:25px;
            height:25px;
            border-radius:50%;
            color:#fff;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:11px;
            font-weight:bold;
        }
        .passed { background:#0f9d58; }
        .failed { background:#d93025; }
        .sort-btn { cursor:pointer; margin-left:5px; font-size:14px; }
        .sort-btn:hover { color:#0d6efd; }
        .loader-placeholder { width:100%; height:18px; background:#2a2a2a; animation:pulse 1.4s infinite; border-radius:4px; color:#fff; }
        @keyframes pulse { 0%{opacity:.4} 50%{opacity:1} 100%{opacity:.4} }
        @media(max-width:576px) { .table-responsive { overflow-x:auto; } .sort-btn { font-size:12px; } }
    </style>
</head>
<body>

<div class="container mt-5">

    <h2 class="text-center mb-4">📘 Learner Progress Dashboard <span class="col-md-2 col-6">
            <span id="clearFilters" class="btn btn-secondary w-100">
                Clear Filters
            </span>
        </span></h2>

    <div id="response"></div>

    <!-- FILTERS -->
    <div class="row g-2 mb-4">
        <div class="col-md-4 col-12">
            <input type="text" id="searchInput" class="form-control" placeholder="Search ID, Name, Surname">
        </div>
        <div class="col-md-4 col-12">
            <select id="courseFilter" class="form-select">
                <option value="">All Courses</option>
            </select>
        </div>
        <div class="col-md-4 col-12">
            <button id="applyFilters" class="btn btn-primary w-100">Apply Filters</button>
        </div>

    </div>

    <div id="learnersContainer"></div>

    <nav class="mt-4">
        <ul id="pagination" class="pagination justify-content-center flex-wrap"></ul>
    </nav>
</div>


    <script>
        $(document).ready(function() {
            let sortBy = null;
            let sortDir = null;

            // Load initial data
            loadCourses();
            loadLearners(1);
            $('#clearFilters').click(function () {
                $('#searchInput').val('');
                $('#courseFilter').val('');
                sortBy = null;
                sortDir = null;
                loadLearners(1);
            });

            // Filters events
            $('#applyFilters').click(() => loadLearners(1));
            $('#searchInput').keyup(e => { if(e.key === "Enter") loadLearners(1); });

            // Sorting click
            $(document).on('click', '.sort-btn', function () {
                sortBy = $(this).data('sort');
                sortDir = $(this).data('dir');
                loadLearners(1);
            });

            function loadCourses() {
                $.ajax({
                    url: "/api/active-courses/list",
                    method: "GET",
                    success: function(response) {
                        if(!response.success){
                            $('#response').html(`<div class="alert alert-danger">${response.message}</div>`);
                            return;
                        }
                        response.data.forEach(c => {
                            $('#courseFilter').append(`<option value="${c.id}">${c.name}</option>`);
                        });
                    }
                });
            }

            function loadLearners(page) {
                $('#learnersContainer').html(`
            <div class="card p-4 mb-3"><div class="loader-placeholder"></div></div>
            <div class="card p-4 mb-3"><div class="loader-placeholder"></div></div>
        `);

                $.ajax({
                    url: `/api/learner-progress?page=${page}`,
                    method: "GET",
                    data: {
                        search: $('#searchInput').val(),
                        filterby: $('#courseFilter').val(),
                        sort_by: sortBy,
                        sort_dir: sortDir
                    },
                    success: function(response){
                        if(!response.success){
                            $('#learnersContainer').html(`<div class="alert alert-danger">${response.message}</div>`);
                            return;
                        }
                        renderLearners(response);
                        renderPagination(response.meta);
                    }
                });
            }

            function renderLearners(response){
                let html = "";

                response.data.forEach(learner => {
                    let avg = learner.courses.length
                        ? (learner.courses.reduce((a,c)=>a+parseInt(c.progress),0) / learner.courses.length)
                        : 0;

                    html += `<div class="card p-3 mb-3">
                <div class="d-flex flex-column flex-sm-row justify-content-between">
                    <h5>${learner.full_name}</h5>
                    <div>
                        <small>ID: ${learner.id}</small>
                        <i class="bi bi-caret-up-fill sort-btn" data-sort="id" data-dir="asc"></i>
                        <i class="bi bi-caret-down-fill sort-btn" data-sort="id" data-dir="desc"></i>
                    </div>
                </div>

                <div class="mt-3">
                    <strong>Average Progress:</strong>
                    <div class="progress mt-1" style="height: 18px;">
                        <div class="progress-bar" role="progressbar"
                             style="width:${avg}%; background:#0d6efd;">
                             ${avg.toFixed(0)}%
                        </div>
                    </div>
                    <i class="bi bi-caret-up-fill sort-btn" data-sort="average_progress" data-dir="asc"></i>
                    <i class="bi bi-caret-down-fill sort-btn" data-sort="average_progress" data-dir="desc"></i>
                </div>

                <div class="mt-3 d-flex flex-wrap">`;

                    if(!learner.courses.length){
                        html += `<p class="text-muted">No courses enrolled.</p>`;
                    } else {
                        learner.courses.forEach(course => {
                            let pass = course.progress >= 50;
                            let statusClass = pass ? "passed" : "failed";
                            let hoverText = pass ? "Passed" : "Failed";

                            html += `<div class="course-pill" title="${hoverText}">
                        ${course.course_name}
                        <div class="progress-circle ${statusClass}">${course.progress}%</div>
                        <i class="bi bi-caret-up-fill sort-btn"
                           data-sort="course_progress_${course.course_id}" data-dir="asc"></i>
                        <i class="bi bi-caret-down-fill sort-btn"
                           data-sort="course_progress_${course.course_id}" data-dir="desc"></i>
                    </div>`;
                        });
                    }

                    html += `</div></div>`;
                });

                $('#learnersContainer').html(html);
            }

            function renderPagination(meta){
                let html = "";
                html += `<li class="page-item ${meta.current_page===1?'disabled':''}">
                    <a class="page-link" data-page="${meta.current_page-1}" href="#">Previous</a>
                 </li>`;

                for(let i=1;i<=meta.last_page;i++){
                    html += `<li class="page-item ${meta.current_page===i?'active':''}">
                        <a class="page-link" data-page="${i}" href="#">${i}</a>
                     </li>`;
                }

                html += `<li class="page-item ${meta.current_page===meta.last_page?'disabled':''}">
                    <a class="page-link" data-page="${meta.current_page+1}" href="#">Next</a>
                 </li>`;

                $('#pagination').html(html);
                $('#pagination .page-link').click(function(e){
                    e.preventDefault();
                    loadLearners($(this).data('page'));
                });
            }
        });
    </script>

</body>
</html>
