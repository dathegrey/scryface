<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>scryface</title>
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedGender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $selectedEyeColor = isset($_POST['eye-color']) ? $_POST['eye-color'] : '';
    $selectedHairColor = isset($_POST['hair-color']) ? $_POST['hair-color'] : '';
    $selectedSkinTone = isset($_POST['skin-tone']) ? $_POST['skin-tone'] : '';
    $selectedTags = isset($_POST['tags']) ? $_POST['tags'] : [];
    $selectedName = isset($_POST['search-bar']) ? $_POST['search-bar'] : '';
  } else {
    $selectedGender = '';
    $selectedEyeColor = '';
    $selectedHairColor = '';
    $selectedSkinTone = '';
    $selectedTags = [];
    $selectedName = '';
  }
  ?>
  <form id="search-form" method="POST" action="">
    <input type="text" id="search-bar" name="search-bar" placeholder="Name" <?= ($selectedName !== '') ? 'value="' . htmlspecialchars($selectedName) . '"' : '' ?>>
    <select name="gender" id="gender-select" onchange='this.form.submit()'>
      <option value="">All Genders</option>
      <option value="gender-male" <?= ($selectedGender === 'gender-male') ? 'selected' : '' ?>>Male</option>
      <option value="gender-female" <?= ($selectedGender === 'gender-female') ? 'selected' : '' ?>>Female</option>
      <option value="gender-enby" <?= ($selectedGender === 'gender-enby') ? 'selected' : '' ?>>Non-Binary</option>
    </select>
    <select name="eye-color" id="eye-color-select" onchange='this.form.submit()'>
      <option value="">All Eye Colors</option>
      <option value="blue-eyes" <?= ($selectedEyeColor === 'blue-eyes') ? 'selected' : '' ?>>Blue</option>
      <option value="green-eyes" <?= ($selectedEyeColor === 'green-eyes') ? 'selected' : '' ?>>Green</option>
      <option value="brown-eyes" <?= ($selectedEyeColor === 'brown-eyes') ? 'selected' : '' ?>>Brown</option>
      <option value="hazel-eyes" <?= ($selectedEyeColor === 'hazel-eyes') ? 'selected' : '' ?>>Hazel</option>
      <option value="gray-eyes" <?= ($selectedEyeColor === 'gray-eyes') ? 'selected' : '' ?>>Gray</option>
    </select>
    <select name="hair-color" id="hair-color-select" onchange='this.form.submit()'>
      <option value="">All Hair Colors</option>
      <option value="blonde-hair" <?= ($selectedHairColor === 'blonde-hair') ? 'selected' : '' ?>>Blonde</option>
      <option value="brown-hair" <?= ($selectedHairColor === 'brown-hair') ? 'selected' : '' ?>>Brown</option>
      <option value="black-hair" <?= ($selectedHairColor === 'black-hair') ? 'selected' : '' ?>>Black</option>
      <option value="red-hair" <?= ($selectedHairColor === 'red-hair') ? 'selected' : '' ?>>Red</option>
      <option value="gray-hair" <?= ($selectedHairColor === 'gray-hair') ? 'selected' : '' ?>>Gray</option>
    </select>
    <select name="skin-tone" id="skin-tone-select" onchange='this.form.submit()'>
      <option value="">All Skin Tones</option>
      <option value="light-skin" <?= ($selectedSkinTone === 'light-skin') ? 'selected' : '' ?>>Light</option>
      <option value="medium-skin" <?= ($selectedSkinTone === 'medium-skin') ? 'selected' : '' ?>>Medium</option>
      <option value="dark-skin" <?= ($selectedSkinTone === 'dark-skin') ? 'selected' : '' ?>>Dark</option>
    </select>
    <button type="submit" name="submit-btn" id="search-button" value="Submit">Search</button>
    <button type="button" id="reset-btn" onclick="location.href=''">Reset</button>
    <div class="tag-cloud">
      <?php
      $tags = file('assets/tags.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($tags as $tag) {
        if (in_array($tag, $selectedTags)) {
          echo "<input type='checkbox' name='tags[]' id='$tag' value='$tag' class='tag-btn' checked onchange='this.form.submit()'> <label class='tag-label' for='$tag'>$tag</label> ";
        } else {
          echo "<input type='checkbox' name='tags[]' id='$tag' value='$tag' class='tag-btn' onchange='this.form.submit()'> <label class='tag-label' for='$tag'>$tag</label> ";
        }
      } 
      ?>
    </div>
  </form>
  <div id="results" class="card-container">
    <!--?php
    echo "Gender: " . htmlspecialchars($selectedGender) . "<br>";
    echo "Eye Color: " . htmlspecialchars($selectedEyeColor) . "<br>";
    echo "Hair Color: " . htmlspecialchars($selectedHairColor) . "<br>";
    echo "Skin Tone: " . htmlspecialchars($selectedSkinTone) . "<br>";
    echo "Tags: " . ($selectedTags === [] ? 'None' : htmlspecialchars(implode(', ', $selectedTags))) . "<br>";
    ?-->
    <br />
    <?php
    $queryString = "SELECT * FROM faces WHERE name LIKE '%" . htmlspecialchars($_POST['search-bar'] ?? '') . "%'";
    if ($selectedGender != '') {
      $queryString .= " AND tags LIKE '%" . htmlspecialchars($selectedGender ?? '') . "%'";
    }
    if ($selectedEyeColor != '') {
      $queryString .= " AND tags LIKE '%" . htmlspecialchars($selectedEyeColor ?? '') . "%'";
    }
    if ($selectedHairColor != '') {
      $queryString .= " AND tags LIKE '%" . htmlspecialchars($selectedHairColor ?? '') . "%'";
    }
    if ($selectedSkinTone != '') {
      $queryString .= " AND tags LIKE '%" . htmlspecialchars($selectedSkinTone ?? '') . "%'";
    }
    foreach ($selectedTags as $tag) {
      $queryString .= " AND tags LIKE '%" . htmlspecialchars($tag ?? '') . "%'";
    }
    // echo "Query: " . $queryString . "<br>";

    // load results from database
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
        $dbPath = __DIR__ . '/assets/data.sqlite';
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $pdo->query($queryString);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
          echo "No results found.";
        } else {
          foreach ($results as $row) {
            echo "<div class='card'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            echo "<img src='" . htmlspecialchars($row['url']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
            echo "<p>Tags: " . htmlspecialchars($row['tags']) . "</p>";
            echo "</div>";
          }
        }
      } catch (PDOException $e) {
        echo "Error connecting to database: " . $e->getMessage();
      }
    }
    ?>
  </div>
</body>
</html>