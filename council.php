<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Council Dashboard</title>
</head>
<body>
    <!-- Dashboard Header -->
    <header class="dashboard-header">
        <img src="images/logo-dark.svg" alt="Council Logo" class="logo">
        <div class="profile-section">
            <i class="bi bi-person-circle profile-icon"></i>
        </div>
    </header>
    
    <!-- Dashboard Nav -->
    <nav class="dashboard-nav mt-4 d-flex justify-content-center align-items-center">
        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">View Analytics</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="add-area-tab" data-bs-toggle="tab" data-bs-target="#add-area" type="button" role="tab">Add Area</button>
            </li>
        </ul>
    </nav>
    
    <!-- Dashboard Content -->
    <main class="dashboard-content">
        <div class="tab-content" id="dashboardTabsContent">
            <!-- Analytics Tab -->
            <div class="tab-pane fade show active" id="analytics" role="tabpanel">
                <h2 class="mb-4 mt-4">Product Analytics</h2>
                <div class="chart-container">
                    <canvas id="analyticsChart"></canvas>
                </div>
            </div>
            
            <!-- Add Area Tab -->
            <div class="tab-pane fade" id="add-area" role="tabpanel">
                <h2 class="mb-4">Add New Area</h2>

                <form class="add-area-form d-flex flex-column justify-content-center align-items-center">
                    <div class="mb-3 w-100">
                        <div>
                            <label for="city" class="form-label">City</label>
                            <select class="form-select" id="city" required>
                                <option value="" selected disabled>Select City</option>
                                <option value="london">London</option>
                                <option value="manchester">Manchester</option>
                                <option value="birmingham">Birmingham</option>
                                <option value="leeds">Leeds</option>
                            </select>
                        </div>
                        <div>
                            <label for="county" class="form-label">County</label>
                            <select class="form-select" id="county" required>
                                <option value="" selected disabled>Select County</option>
                                <option value="greater-london">Greater London</option>
                                <option value="greater-manchester">Greater Manchester</option>
                                <option value="west-midlands">West Midlands</option>
                                <option value="west-yorkshire">West Yorkshire</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 w-100">
                        <div>
                            <label for="postcode" class="form-label">Postcode</label>
                            <input type="text" class="form-control form-select" id="postcode" placeholder="e.g. SW1A 1AA" required>
                        </div>
                        <div>
                            <label for="country" class="form-label">Country</label>
                            <select class="form-select" id="country" required>
                                <option value="uk" selected>United Kingdom</option>
                                <option value="us">United States</option>
                                <option value="ca">Canada</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn hero-btn">Add Area</button>
                </form>
                
                <!-- Areas Table -->
                <div class="areas-table">
                    <h3 class="mb-3">Added Areas</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>City</th>
                                    <th>County</th>
                                    <th>Country</th>
                                    <th>Postcode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Hatfield</td>
                                    <td>Hertfordshire</td>
                                    <td>United Kingdom</td>
                                    <td>UZ07 1MA</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hatfield</td>
                                    <td>Hertfordshire</td>
                                    <td>United Kingdom</td>
                                    <td>UZ07 1MA</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hatfield</td>
                                    <td>Hertfordshire</td>
                                    <td>United Kingdom</td>
                                    <td>UZ07 1MA</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const analyticsChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Yoga Mats', 'Organic Fruits', 'Eco Cleaning', 'Sustainable Clothing', 'Mindfulness Books'],
                datasets: [{
                    data: [35, 25, 15, 15, 10],
                    backgroundColor: [
                        '#00A251',
                        '#2ECC71',
                        '#3498DB',
                        '#9B59B6',
                        '#F1C40F'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Product Category Distribution',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });

        // Form submission handler
        document.querySelector('.add-area-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            alert('Area added successfully!');
        });
    </script>
</body>
</html>