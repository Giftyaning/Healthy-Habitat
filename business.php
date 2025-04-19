<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Business Dashboard</title>

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
                <button class="nav-link active" id="listings-tab" data-bs-toggle="tab" data-bs-target="#listings" type="button" role="tab">Products / Services</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Ratings</button>
            </li>
        </ul>
    </nav>
    
    <!-- Dashboard Content -->
    <main class="dashboard-content">
        <div class="tab-content" id="dashboardTabsContent">
            
        <!-- Products / Services -->
            <div class="tab-pane fade show active" id="listings" role="tabpanel">
                <div class="products-top mb-5">
                    <h2 class="mb-4">Products / Services</h2>
                    <button class="btn-add">Add Product / Services</button>  
                </div>
                
                <div class="table-responsive data-table">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Certification</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Organic Yoga Mat</td>
                                <td>Fitness</td>
                                <td>£29.99</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                            </tr>
                            <tr>
                                <td>Natural Deodorant</td>
                                <td>Beauty</td>
                                <td>£12.99</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                            </tr>
                            <tr>
                                <td>Bamboo Toothbrush Set</td>
                                <td>Home</td>
                                <td>£8.99</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                            </tr>
                            <tr>
                                <td>Meditation Cushion</td>
                                <td>Mindfulness</td>
                                <td>£24.99</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                            </tr>
                            <tr>
                                <td>Organic Green Tea</td>
                                <td>Food</td>
                                <td>£5.99</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Products Ratings -->
            <div class="tab-pane fade show active" id="analytics" role="tabpanel">
                <h2 class="mb-4 mt-4">Product Ratings</h2>
                <div class="chart-container">
                    <canvas id="analyticsChart"></canvas>
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