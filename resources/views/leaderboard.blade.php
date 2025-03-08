<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .leaderboard-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container leaderboard-container">
    <h2 class="text-center mb-4">Leaderboard</h2>

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="filter" class="form-label">Filter:</label>
            <select id="filter" class="form-select">
                <option value="all">All Time</option>
                <option value="day">Today</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search" class="form-label">Search by User Name:</label>
            <input type="text" id="search" class="form-control" placeholder="Enter User Name">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button id="recalculate-btn" class="btn btn-primary w-100">Recalculate</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>Rank</th>
                    <th>Full Name</th>
                    <th>Total Points</th>
                </tr>
            </thead>
            <tbody id="leaderboard-body"></tbody>
        </table>
        <div id="pagination-controls" class="text-center mt-3"></div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function fetchLeaderboard(page = 1) {
    let filter = $('#filter').val();
    let userName = $('#search').val();

    $.ajax({
        url: '/leaderboard?page=' + page,
        type: 'GET',
        data: { filter: filter, user_name: userName },
        success: function (data) {
            $('#leaderboard-body').html('');

            data.data.forEach((user, index) => {
                $('#leaderboard-body').append(`
                    <tr>
                        <td>${user.rank}</td>
                        <td>${user.name}</td>
                        <td>${user.total_points}</td>
                    </tr>
                `);
            });

            let paginationHtml = `<nav><ul class="pagination justify-content-center">`;

            if (data.prev_page_url) {
                paginationHtml += `<li class="page-item">
                    <a class="page-link" href="#" onclick="fetchLeaderboard(${data.current_page - 1})">Previous</a>
                </li>`;
            }

            for (let i = 1; i <= data.last_page; i++) {
                paginationHtml += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="fetchLeaderboard(${i})">${i}</a>
                </li>`;
            }

            if (data.next_page_url) {
                paginationHtml += `<li class="page-item">
                    <a class="page-link" href="#" onclick="fetchLeaderboard(${data.current_page + 1})">Next</a>
                </li>`;
            }

            paginationHtml += `</ul></nav>`;
            $('#pagination-controls').html(paginationHtml);
        }
    });
}

$(document).ready(function () {
    fetchLeaderboard();

    $('#filter').change(fetchLeaderboard);
    $('#search').on('keyup', fetchLeaderboard);

    $('#recalculate-btn').click(function () {
        $.ajax({
            url: '/recalculate',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' }, 
            success: function () {
                alert("Leaderboard recalculated successfully!");
                fetchLeaderboard(); 
            },
            error: function () {
                alert("Error recalculating leaderboard.");
            }
        });
    });
});



    </script>

</body>
</html>
