<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Omega CMMS API Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Omega CMMS API Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Authentication Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Authentication</h2>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Login:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">POST /api/v1/login</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Logout:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">POST /api/v1/logout</code>
                    </div>
                </div>
            </div>

            <!-- Asset Management Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Asset Management</h2>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Facilities:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">GET /api/v1/facilities</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Assets:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">GET /api/v1/assets</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Work Orders:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">GET /api/v1/work-orders</code>
                    </div>
                </div>
            </div>

            <!-- Inventory Management Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Inventory Management</h2>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">References:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">GET /api/v1/inventory/references</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Transactions:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">GET /api/v1/inventory/transactions</code>
                    </div>
                </div>
            </div>

            <!-- Reports Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Reports</h2>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Usage Report:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">GET /api/v1/reports/usage</code>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Audit Report:</span>
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">GET /api/v1/reports/audit</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Credentials -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Test Credentials</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">Administrator</td>
                            <td class="px-4 py-3 whitespace-nowrap">admin@omegacmms.com</td>
                            <td class="px-4 py-3 whitespace-nowrap">Admin@123</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">Manager</td>
                            <td class="px-4 py-3 whitespace-nowrap">manager@omegacmms.com</td>
                            <td class="px-4 py-3 whitespace-nowrap">Manager@123</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">Technician</td>
                            <td class="px-4 py-3 whitespace-nowrap">tech@omegacmms.com</td>
                            <td class="px-4 py-3 whitespace-nowrap">Tech@123</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- API Testing Form -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Quick API Test</h2>
            <div id="api-test" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Login</label>
                    <button onclick="testLogin()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Test Admin Login
                    </button>
                    <div id="login-result" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function testLogin() {
            const resultDiv = document.getElementById('login-result');
            resultDiv.innerHTML = '<div class="text-gray-600">Testing login...</div>';
            
            try {
                const response = await fetch('/api/v1/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: 'admin@omegacmms.com',
                        password: 'Admin@123'
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    resultDiv.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            <strong>Success!</strong> Token received: ${data.token.substring(0, 20)}...
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Error:</strong> ${data.message || 'Login failed'}
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
            }
        }
    </script>
</body>
</html>