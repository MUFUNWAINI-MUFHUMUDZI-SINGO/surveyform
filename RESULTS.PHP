<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "survey";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Fetch all data
$result = $conn->query("SELECT * FROM survey_responses");
$surveys = [];
while ($row = $result->fetch_assoc()) {
    $surveys[] = $row;
}

// 3. Calculate total surveys
$total_surveys = count($surveys);

// 4. Calculate age stats
$ages = array_map(function($s) {
    return date_diff(date_create($s['dob']), date_create('today'))->y;
}, $surveys);
$average_age = $total_surveys ? round(array_sum($ages) / $total_surveys, 1) : 0;
$max_age = $total_surveys ? max($ages) : 0;
$min_age = $total_surveys ? min($ages) : 0;

// 5. Count favorite food choices (fixed for multiple foods)
$food_counts = ['Pizza' => 0, 'Pasta' => 0, 'Pap and Wors' => 0];
foreach ($surveys as $s) {
    $foods = explode(',', $s['favorite_food']);
    foreach ($foods as $food) {
        $food = trim($food);
        if (isset($food_counts[$food])) {
            $food_counts[$food]++;
        }
    }
}
$percent = function($count) use ($total_surveys) {
    return $total_surveys ? round(($count / $total_surveys) * 100, 1) : 0;
};

// 6. Average ratings
$avg = function($field) use ($surveys, $total_surveys) {
    return $total_surveys ? round(array_sum(array_column($surveys, $field)) / $total_surveys, 1) : 0;
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Survey Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-100 min-h-screen font-sans">

  <!-- Navbar -->
  <nav class="bg-white shadow-md py-4 px-8 flex justify-between items-center sticky top-0 z-50">
    <h1 class="text-2xl font-bold text-gray-800">Tshimo Surveys</h1>
    <div class="space-x-8">
      <a href="INDEX.php" class="text-blue-700 font-semibold hover:text-blue-900 transition">FILL OUT SURVEY</a>
      <a href="RESULTS.php" class="text-blue-700 font-semibold hover:text-blue-900 transition">VIEW SURVEY RESULTS</a>
    </div>
  </nav>

  <!-- Form Section -->
  <main class="max-w-4xl mx-auto mt-12 bg-white shadow-xl rounded-lg p-10">
    <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-12">Lifestyle Preferences Survey Results</h2>

    <div class="space-y-8">

      <!-- General Stats -->
      <div class="bg-blue-50 p-6 rounded-lg shadow-sm">
        <h3 class="text-xl font-semibold text-blue-800 mb-4">Survey Overview</h3>
        <table class="w-full text-left border border-blue-200">
          <tr class="bg-blue-100 font-semibold">
            <th class="p-2 border">Total Surveys</th>
            <th class="p-2 border">Average Age</th>
            <th class="p-2 border">Oldest Age</th>
            <th class="p-2 border">Youngest Age</th>
          </tr>
          <tr class="bg-white">
            <td class="p-2 border"><?= $total_surveys ?></td>
            <td class="p-2 border"><?= $average_age ?></td>
            <td class="p-2 border"><?= $max_age ?></td>
            <td class="p-2 border"><?= $min_age ?></td>
          </tr>
        </table>
      </div>

      <!-- Food Preferences -->
      <div class="bg-purple-50 p-6 rounded-lg shadow-sm">
        <h3 class="text-xl font-semibold text-purple-800 mb-4">Favorite Food Preferences</h3>
        <table class="w-full text-left border border-purple-200">
          <tr class="bg-purple-100 font-semibold">
            <th class="p-2 border">Food</th>
            <th class="p-2 border">Percentage</th>
          </tr>
          <tr class="bg-white">
            <td class="p-2 border">Pizza</td>
            <td class="p-2 border"><?= $percent($food_counts['Pizza']) ?>%</td>
          </tr>
          <tr class="bg-white">
            <td class="p-2 border">Pasta</td>
            <td class="p-2 border"><?= $percent($food_counts['Pasta']) ?>%</td>
          </tr>
          <tr class="bg-white">
            <td class="p-2 border">Pap and Wors</td>
            <td class="p-2 border"><?= $percent($food_counts['Pap and Wors']) ?>%</td>
          </tr>
        </table>
      </div>

      <!-- Activity Ratings -->
      <div class="bg-green-50 p-6 rounded-lg shadow-sm">
        <h3 class="text-xl font-semibold text-green-800 mb-4">Lifestyle Activity Ratings (out of 5)</h3>
        <table class="w-full text-left border border-green-200">
          <tr class="bg-green-100 font-semibold">
            <th class="p-2 border">Activity</th>
            <th class="p-2 border">Average Rating</th>
          </tr>
          <tr class="bg-white">
            <td class="p-2 border">Watch Movies</td>
            <td class="p-2 border"><?= $avg('watch_movies') ?></td>
          </tr>
          <tr class="bg-white">
            <td class="p-2 border">Listen to Radio</td>
            <td class="p-2 border"><?= $avg('listen_radio') ?></td>
          </tr>
          <tr class="bg-white">
            <td class="p-2 border">Eat Out</td>
            <td class="p-2 border"><?= $avg('eat_out') ?></td>
          </tr>
          <tr class="bg-white">
            <td class="p-2 border">Watch TV</td>
            <td class="p-2 border"><?= $avg('watch_tv') ?></td>
          </tr>
        </table>
      </div>

    </div>
  </main>
</body>
</html>
