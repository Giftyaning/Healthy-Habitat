<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>Admin Dashboard</title>

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
                <button class="nav-link active" id="listings-tab" data-bs-toggle="tab" data-bs-target="#listings" type="button" role="tab">Listings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Pending</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="companies-tab" data-bs-toggle="tab" data-bs-target="#companies" type="button" role="tab">Companies</button>
            </li>
        </ul>
    </nav>
    
    <!-- Dashboard Content -->
    <main class="dashboard-content">
        <div class="tab-content" id="dashboardTabsContent">
            
        <!-- Listings Tab -->
            <div class="tab-pane fade show active" id="listings" role="tabpanel">
                <h2 class="mb-4">All Companies</h2>
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
            
            <!-- Pending Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel">
                <h2 class="mb-4">Pending Approvals</h2>
                <div class="table data-table d-flex flex-column justify-content-center align-items-center">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Product/Service</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Certificate</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                           <tr>
                                <td>EcoLiving Ltd</td>
                                <td>Reusable Water Bottle</td>
                                <td>Home</td>
                                <td>£14.99</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                                <td>
                                    <select class="form-select form-select-sm status-dropdown">
                                        <option value="pending" selected>Pending</option>
                                        <option value="approve">Approve</option>
                                        <option value="decline">Decline</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>PureBeauty</td>
                                <td>Organic Face Cream</td>
                                <td>Beauty</td>
                                <td>£22.50</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                                <td>
                                    <select class="form-select form-select-sm status-dropdown">
                                        <option value="pending" selected>Pending</option>
                                        <option value="approve">Approve</option>
                                        <option value="decline">Decline</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>MindfulLiving</td>
                                <td>Meditation App Subscription</td>
                                <td>Mindfulness</td>
                                <td>£9.99/month</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                                <td>
                                    <select class="form-select form-select-sm status-dropdown">
                                        <option value="pending">Pending</option>
                                        <option value="approve" selected>Approve</option>
                                        <option value="decline">Decline</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>GreenHarvest</td>
                                <td>Organic Vegetables Box</td>
                                <td>Food</td>
                                <td>£35.00</td>
                                <td><a href="#" class="view-certificate">View</a></td>
                                <td>
                                    <select class="form-select form-select-sm status-dropdown">
                                        <option value="pending">Pending</option>
                                        <option value="approve">Approve</option>
                                        <option value="decline" selected>Decline</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button class="btn hero-btn mt-4" type="submit">Confirm</button>
                </div>
            </div>
            
            <!-- Companies Tab -->
            <div class="tab-pane fade" id="companies" role="tabpanel">
                <h2 class="mb-4">Registered Companies</h2>
                <div class="table-responsive data-table">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Email Address</th>
                                <th>Contact Number</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr>
                                <td>Natural Wellness Ltd</td>
                                <td>contact@naturalwellness.com</td>
                                <td>+44 20 1234 5678</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>EcoHome Solutions</td>
                                <td>info@ecohomesolutions.co.uk</td>
                                <td>+44 20 2345 6789</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>PureMind Therapies</td>
                                <td>hello@puremind.com</td>
                                <td>+44 20 3456 7890</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>GreenEarth Foods</td>
                                <td>sales@greenearthfoods.org</td>
                                <td>+44 20 4567 8901</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Sustainable Living</td>
                                <td>support@sustainableliving.uk</td>
                                <td>+44 20 5678 9012</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.status-dropdown').forEach(dropdown => {
            dropdown.addEventListener('change', function() {
                const row = this.closest('tr');
                if (this.value === 'approve') {
                    row.style.backgroundColor = 'rgba(0, 162, 81, 0.1)';
                } else if (this.value === 'decline') {
                    row.style.backgroundColor = 'rgba(239, 56, 38, 0.1)';
                } else {
                    row.style.backgroundColor = '';
                }
            });
        });
    </script>
</body>
</html>