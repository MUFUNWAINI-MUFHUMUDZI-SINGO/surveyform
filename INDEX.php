<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "survey";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['full_name']) ? $conn->real_escape_string(trim($_POST['full_name'])) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $dob = isset($_POST['dob']) ? $conn->real_escape_string(trim($_POST['dob'])) : '';
    $contact_number = isset($_POST['contact_number']) ? $conn->real_escape_string(trim($_POST['contact_number'])) : '';
    $favorite_foods = isset($_POST['favorite_food']) ? $_POST['favorite_food'] : [];
    
    $escaped_foods = array_map(function($food) use ($conn) {
        return $conn->real_escape_string($food);
    }, $favorite_foods);
    $favorite_food = implode(", ", $escaped_foods);

    $watch_movies = isset($_POST['watch_movies']) ? intval($_POST['watch_movies']) : 0;
    $listen_radio = isset($_POST['listen_radio']) ? intval($_POST['listen_radio']) : 0;
    $eat_out = isset($_POST['eat_out']) ? intval($_POST['eat_out']) : 0;
    $watch_tv = isset($_POST['watch_tv']) ? intval($_POST['watch_tv']) : 0;

    // You might want to validate here on server side as well.

    $sql = "INSERT INTO survey_responses (
        full_name, email, dob, contact_number, favorite_food,
        watch_movies, listen_radio, eat_out, watch_tv
    ) VALUES (
        '$full_name', '$email', '$dob', '$contact_number', '$favorite_food',
        $watch_movies, $listen_radio, $eat_out, $watch_tv
    )";

  }

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <title>Survey Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom styles for error messages */
    .error-message {
      color: #dc2626; /* Tailwind red-600 */
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
    input:invalid, select:invalid {
      border-color: #dc2626;
      box-shadow: 0 0 0 1px #dc2626;
    }
  </style>
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
    <h2 class="text-4xl font-extrabold text-center text-gray-800 mb-12">Lifestyle Preferences Survey</h2>

    <form id="surveyForm" method="POST" novalidate class="space-y-10">

      <!-- Personal Details -->
      <section>
        <h3 class="text-2xl font-semibold text-gray-700 mb-6 border-b pb-2">Personal Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label for="full_name" class="block mb-2 font-medium text-gray-600">Full Name *</label>
            <input
              type="text"
              id="full_name"
              name="full_name"
              placeholder="Enter your full name"
              required
              class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p class="error-message hidden" id="error_full_name">Full name is required.</p>
          </div>

          <div>
            <label for="email" class="block mb-2 font-medium text-gray-600">Email *</label>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Enter your email"
              required
              class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p class="error-message hidden" id="error_email">Valid email is required.</p>
          </div>

          <div>
            <label for="dob" class="block mb-2 font-medium text-gray-600">Date of Birth *</label>
            <input
              type="date"
              id="dob"
              name="dob"
              required
              class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
              max="<?= date('Y-m-d'); ?>"
            />
            <p class="error-message hidden" id="error_dob">You must be between 5 and 120 years old.</p>
          </div>

          <div>
            <label for="contact_number" class="block mb-2 font-medium text-gray-600">Contact Number *</label>
            <input
              type="text"
              id="contact_number"
              name="contact_number"
              placeholder="Enter your contact number"
              required
              pattern="^[0-9+()\-\s]{7,20}$"
              class="p-3 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <p class="error-message hidden" id="error_contact_number">Please enter a valid contact number.</p>
          </div>
        </div>
      </section>

      <!-- Favorite Food -->
      <section>
        <h3 class="text-2xl font-semibold text-gray-700 mb-6 border-b pb-2">Favorite Food</h3>
        <div class="space-y-3 pl-4 text-gray-700">
          <label class="inline-flex items-center space-x-3 cursor-pointer">
            <input type="checkbox" name="favorite_food[]" value="Pizza" class="accent-blue-600" />
            <span>Pizza</span>
          </label>
          <label class="inline-flex items-center space-x-3 cursor-pointer">
            <input type="checkbox" name="favorite_food[]" value="Pasta" class="accent-blue-600" />
            <span>Pasta</span>
          </label>
          <label class="inline-flex items-center space-x-3 cursor-pointer">
            <input type="checkbox" name="favorite_food[]" value="Pap and Wors" class="accent-blue-600" />
            <span>Pap and Wors</span>
          </label>
          <label class="inline-flex items-center space-x-3 cursor-pointer">
            <input type="checkbox" name="favorite_food[]" value="Other" class="accent-blue-600" />
            <span>Other</span>
          </label>
        </div>
      </section>

      <!-- Lifestyle Preferences -->
      <section>
        <h3 class="text-2xl font-semibold text-gray-700 mb-6 border-b pb-2">Lifestyle Preferences</h3>
        <p class="text-gray-600 mb-6 italic">Please rate your level of agreement (1 = Strongly Agree to 5 = Strongly Disagree):</p>

        <div class="overflow-x-auto">
          <table class="min-w-full text-center border border-gray-300 rounded-md">
            <thead class="bg-blue-100 text-gray-700">
              <tr>
                <th class="py-3 px-5 border">Statement</th>
                <th class="py-3 px-4 border">Strongly Agree</th>
                <th class="py-3 px-4 border">Agree</th>
                <th class="py-3 px-4 border">Neutral</th>
                <th class="py-3 px-4 border">Disagree</th>
                <th class="py-3 px-4 border">Strongly disagree</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $questions = [
                'watch_movies' => 'I watch movies regularly.',
                'listen_radio' => 'I listen to the radio often.',
                'eat_out' => 'I like to eat out frequently.',
                'watch_tv' => 'I watch television every day.'
              ];
              foreach ($questions as $name => $text) {
                echo "<tr class='border-t border-gray-300'>";
                echo "<td class='text-left px-4 py-3 font-medium text-gray-700'>{$text}</td>";
                for ($i=1; $i<=5; $i++) {
                  echo "<td class='border-l border-gray-300'>";
                  echo "<input
                    type='radio'
                    name='{$name}'
                    value='{$i}'
                    required
                    class='accent-blue-600 mx-auto block cursor-pointer'
                    aria-label='{$text} rating {$i}'
                  />";
                  echo "</td>";
                }
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
          <p class="error-message hidden" id="error_ratings">Please rate all lifestyle questions.</p>
        </div>
      </section>

      <!-- Submit Button -->
      <div class="text-center">
      

        <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white text-lg px-8 py-3 rounded-lg shadow transition">
          Submit Survey
        </button>

        
       

      </div>
    </form>
  </main>

  <script>
    // Client side validation
    const form = document.getElementById('surveyForm');

    form.addEventListener('submit', (e) => {
      // Clear previous errors
      document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
      let valid = true;

      // Validate required text inputs
      ['full_name', 'email', 'dob', 'contact_number'].forEach(id => {
        const input = form.querySelector(`#${id}`);
        if (!input.value.trim()) {
          valid = false;
          document.getElementById(`error_${id}`).classList.remove('hidden');
          input.focus();
        }
      });

      // Validate DOB for age 5 - 120
      const dobInput = form.querySelector('#dob');
      if (dobInput.value) {
        const dob = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
          age--;
        }
        if (age < 5 || age > 120) {
          valid = false;
          document.getElementById('error_dob').classList.remove('hidden');
          dobInput.focus();
        }
      }

      // Validate that all 4 rating questions have a selection
      const questions = ['watch_movies', 'listen_radio', 'eat_out', 'watch_tv'];
      for (const q of questions) {
        const radios = form.querySelectorAll(`input[name="${q}"]`);
        if (![...radios].some(radio => radio.checked)) {
          valid = false;
          document.getElementById('error_ratings').classList.remove('hidden');
          radios[0].focus();
          break;
        }
      }

      if (!valid) e.preventDefault();
    });
  </script>
</body>
</html>
